<?php

namespace App\EventListener;

use App\Entity\Promise;
use App\Entity\StatusUpdate;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Query\Expr;

class EntityStatusUpdateListener
{
    public function postPersist(LifecycleEventArgs $args)
    {
        if ($args->getEntity() instanceof StatusUpdate) {
            $this->updatePromiseStatus($args->getEntity()->getPromise(), $args->getEntityManager());
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        if ($args->getEntity() instanceof StatusUpdate) {
            $this->updatePromiseStatus($args->getEntity()->getPromise(), $args->getEntityManager());
        }
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        if ($args->getEntity() instanceof StatusUpdate) {
            $this->updatePromiseStatus($args->getEntity()->getPromise(), $args->getEntityManager());
        }
    }

    private function updatePromiseStatus(Promise $promise, ObjectManager $objectManager)
    {
        /** @var StatusUpdate $latestStatusUpdate */
        $latestStatusUpdate = $objectManager->getRepository('App:StatusUpdate')
            ->createQueryBuilder('su')
            ->where('su.promise = :promise AND su.status IS NOT NULL')
            ->innerJoin('App:Action', 'a', Expr\Join::WITH, 'a.id = su.action')
            ->orderBy('a.occurredTime', 'DESC')
            ->setMaxResults(1)
            ->setParameter('promise', $promise)
            ->getQuery()
            ->getOneOrNullResult();

        $promise->setStatus($latestStatusUpdate ? $latestStatusUpdate->getStatus() : null);
        $objectManager->flush();
    }
}
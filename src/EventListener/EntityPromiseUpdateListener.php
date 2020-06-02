<?php

namespace App\EventListener;

use App\Entity\Promise;
use App\Entity\PromiseUpdate;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Query\Expr;

class EntityPromiseUpdateListener
{
    public function postPersist(LifecycleEventArgs $args)
    {
        if ($args->getEntity() instanceof PromiseUpdate) {
            $this->updatePromiseStatus($args->getEntity()->getPromise(), $args->getEntityManager());
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        if ($args->getEntity() instanceof PromiseUpdate) {
            $this->updatePromiseStatus($args->getEntity()->getPromise(), $args->getEntityManager());
        }
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        if ($args->getEntity() instanceof PromiseUpdate) {
            $this->updatePromiseStatus($args->getEntity()->getPromise(), $args->getEntityManager());
        }
    }

    private function updatePromiseStatus(Promise $promise, ObjectManager $objectManager)
    {
        /** @var PromiseUpdate $latestPromiseUpdate */
        $latestPromiseUpdate = $objectManager->getRepository('App:PromiseUpdate')
            ->createQueryBuilder('su')
            ->where('su.promise = :promise AND su.status IS NOT NULL')
            ->innerJoin('App:PromiseAction', 'a', Expr\Join::WITH, 'a.id = su.action')
            ->orderBy('a.occurredTime', 'DESC')
            ->setMaxResults(1)
            ->setParameter('promise', $promise)
            ->getQuery()
            ->getOneOrNullResult();

        $promise->setStatus($latestPromiseUpdate ? $latestPromiseUpdate->getStatus() : null);
        $objectManager->flush();
    }
}
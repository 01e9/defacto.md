<?php

namespace App\EventSubscriber;

use App\Entity\Promise;
use App\Entity\PromiseUpdate;
use App\Event\PromiseActionUpdatedEvent;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PromiseActionSubscriber implements EventSubscriberInterface
{
    private ObjectManager $objectManager;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            PromiseActionUpdatedEvent::class => [
                ['updatePromisesStatus', 0],
            ]
        ];
    }

    function updatePromisesStatus(PromiseActionUpdatedEvent $event)
    {
        $promises = [];
        foreach ($event->getPromiseAction()->getPromiseUpdates() as $promiseUpdate) {
            /** @var PromiseUpdate $promiseUpdate */
            $promises[ $promiseUpdate->getPromise()->getId() ] = $promiseUpdate->getPromise();
        }

        foreach ($promises as $promise /** @var Promise $promise */) {
            /** @var PromiseUpdate $latestPromiseUpdate */
            $latestPromiseUpdate = $this->objectManager->getRepository(PromiseUpdate::class)
                ->createQueryBuilder('su')
                ->where('su.promise = :promise AND su.status IS NOT NULL')
                ->innerJoin('App:PromiseAction', 'a', Expr\Join::WITH, 'a.id = su.action')
                ->orderBy('a.occurredTime', 'DESC')
                ->setMaxResults(1)
                ->setParameter('promise', $promise)
                ->getQuery()
                ->getOneOrNullResult();

            $promise->setStatus($latestPromiseUpdate ? $latestPromiseUpdate->getStatus() : null);
        }

        $this->objectManager->flush();
    }
}

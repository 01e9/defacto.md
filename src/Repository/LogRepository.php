<?php

namespace App\Repository;

use App\Entity\Action;
use App\Entity\Log;
use App\Entity\Promise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Log|null find($id, $lockMode = null, $lockVersion = null)
 * @method Log|null findOneBy(array $criteria, array $orderBy = null)
 * @method Log[]    findAll()
 * @method Log[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Log::class);
    }

    public function findLatestByPromise(Promise $promise, $limit = 10)
    {
        return $this->findBy(
            ['objectType' => 'promise', 'objectId' => $promise->getId()],
            ['occurredTime' => 'desc'],
            $limit
        );
    }

    public function findLatestByAction(Action $action, $limit = 10)
    {
        return $this->findBy(
            ['objectType' => 'action', 'objectId' => $action->getId()],
            ['occurredTime' => 'desc'],
            $limit
        );
    }
}

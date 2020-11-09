<?php

namespace App\Repository;

use App\Entity\PromiseActionSource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PromiseActionSource|null find($id, $lockMode = null, $lockVersion = null)
 * @method PromiseActionSource|null findOneBy(array $criteria, array $orderBy = null)
 * @method PromiseActionSource[]    findAll()
 * @method PromiseActionSource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromiseActionSourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PromiseActionSource::class);
    }
}

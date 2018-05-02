<?php

namespace App\Repository;

use App\Entity\ActionSource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ActionSource|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActionSource|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActionSource[]    findAll()
 * @method ActionSource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActionSourceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ActionSource::class);
    }

//    /**
//     * @return ActionSource[] Returns an array of ActionSource objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ActionSource
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

<?php

namespace App\Repository;

use App\Entity\PromiseSource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PromiseSource|null find($id, $lockMode = null, $lockVersion = null)
 * @method PromiseSource|null findOneBy(array $criteria, array $orderBy = null)
 * @method PromiseSource[]    findAll()
 * @method PromiseSource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromiseSourceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PromiseSource::class);
    }

//    /**
//     * @return PromiseSource[] Returns an array of PromiseSource objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PromiseSource
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

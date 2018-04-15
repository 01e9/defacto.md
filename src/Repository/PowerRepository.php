<?php

namespace App\Repository;

use App\Entity\Power;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Power|null find($id, $lockMode = null, $lockVersion = null)
 * @method Power|null findOneBy(array $criteria, array $orderBy = null)
 * @method Power[]    findAll()
 * @method Power[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PowerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Power::class);
    }
}

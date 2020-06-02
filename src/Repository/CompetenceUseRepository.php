<?php

namespace App\Repository;

use App\Entity\CompetenceUse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CompetenceUse|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompetenceUse|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompetenceUse[]    findAll()
 * @method CompetenceUse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompetenceUseRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CompetenceUse::class);
    }
}

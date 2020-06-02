<?php

namespace App\Repository;

use App\Entity\CompetenceCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CompetenceCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompetenceCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompetenceCategory[]    findAll()
 * @method CompetenceCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompetenceCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompetenceCategory::class);
    }
}

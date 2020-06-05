<?php

namespace App\Repository;

use App\Entity\MandateCompetenceCategoryStats;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MandateCompetenceCategoryStats|null find($id, $lockMode = null, $lockVersion = null)
 * @method MandateCompetenceCategoryStats|null findOneBy(array $criteria, array $orderBy = null)
 * @method MandateCompetenceCategoryStats[]    findAll()
 * @method MandateCompetenceCategoryStats[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MandateCompetenceCategoryStatsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MandateCompetenceCategoryStats::class);
    }
}

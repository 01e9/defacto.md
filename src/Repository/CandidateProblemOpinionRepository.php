<?php

namespace App\Repository;

use App\Entity\CandidateProblemOpinion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CandidateProblemOpinion|null find($id, $lockMode = null, $lockVersion = null)
 * @method CandidateProblemOpinion|null findOneBy(array $criteria, array $orderBy = null)
 * @method CandidateProblemOpinion[]    findAll()
 * @method CandidateProblemOpinion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CandidateProblemOpinionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CandidateProblemOpinion::class);
    }
}

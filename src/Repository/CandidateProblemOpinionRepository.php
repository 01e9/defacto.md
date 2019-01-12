<?php

namespace App\Repository;

use App\Entity\CandidateProblemOpinion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CandidateProblemOpinion|null find($id, $lockMode = null, $lockVersion = null)
 * @method CandidateProblemOpinion|null findOneBy(array $criteria, array $orderBy = null)
 * @method CandidateProblemOpinion[]    findAll()
 * @method CandidateProblemOpinion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CandidateProblemOpinionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CandidateProblemOpinion::class);
    }
}

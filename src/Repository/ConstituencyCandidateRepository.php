<?php

namespace App\Repository;

use App\Entity\ConstituencyCandidate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ConstituencyCandidate|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConstituencyCandidate|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConstituencyCandidate[]    findAll()
 * @method ConstituencyCandidate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConstituencyCandidateRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ConstituencyCandidate::class);
    }
}

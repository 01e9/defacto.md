<?php

namespace App\Repository;

use App\Entity\ConstituencyProblem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ConstituencyProblem|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConstituencyProblem|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConstituencyProblem[]    findAll()
 * @method ConstituencyProblem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConstituencyProblemRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ConstituencyProblem::class);
    }
}

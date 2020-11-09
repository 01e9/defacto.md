<?php

namespace App\Repository;

use App\Entity\Competence;
use App\Entity\CompetenceUse;
use App\Entity\Title;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method Competence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Competence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Competence[]    findAll()
 * @method Competence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompetenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Competence::class);
    }

    public function hasConnections(string $id) : bool
    {
        return !!$this->getEntityManager()->getRepository(CompetenceUse::class)->findOneBy(['competence' => $id]);
    }

    public function getAdminList(Request $request)
    {
        return $this->createQueryBuilder('c')
            ->join('c.title', 't')
            ->orderBy('t.name', 'ASC')
            ->addOrderBy('c.code', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getAdminChoicesByTitle(Title $title) : array
    {
        $choices = [];

        foreach ($this->findBy(['title' => $title], ['code' => 'ASC']) as $entity) {
            $label = "{$entity->getCode()} ({$entity->getPoints()}) {$entity->getName()}";
            $choices[ $label ] = $entity;
        }

        return $choices;
    }
}

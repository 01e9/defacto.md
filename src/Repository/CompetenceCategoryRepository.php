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

    public function getAdminChoices() : array
    {
        $choices = [];

        foreach ($this->findBy([], ['name' => 'ASC']) as $entity /** @var CompetenceCategory $entity */) {
            if ($entity->getParent()) {
                $parentLabel = $entity->getParent()->getName();

                if (isset($choices[$parentLabel]) && !is_array($choices[$parentLabel])) {
                    $choices[$parentLabel] = [];
                }

                $choices[ $parentLabel ][ $entity->getName() ] = $entity;
            } elseif (!isset($choices[$entity->getName()])) {
                $choices[$entity->getName()] = $entity;
            }
        }

        ksort($choices);

        return $choices;
    }
}

<?php

namespace App\Repository;

use App\Entity\CompetenceCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

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

    /**
     * @param CompetenceCategory|CompetenceCategory[] $categories
     * @return Collection
     */
    public function findWithChildren($categories): Collection
    {
        if (!$categories) {
            return new ArrayCollection();
        }

        $categories = (array) $categories;
        $categoryWithChildren = [];

        foreach ($categories as $category /** @var CompetenceCategory $category */) {
            $categoryWithChildren[$category->getId()] = $category;

            foreach ($this->findBy(['parent' => $categories]) as $childCategory) {
                $categoryWithChildren[$childCategory->getId()] = $childCategory;
            }
        }

        return new ArrayCollection($categoryWithChildren);
    }

    public function getParentChoices() : array
    {
        $choices = [];

        foreach ($this->findBy(['parent' => null], ['name' => 'ASC']) as $entity /** @var CompetenceCategory $entity */) {
            $choices[$entity->getName()] = $entity;
        }

        return $choices;
    }
}

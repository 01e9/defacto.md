<?php

namespace App\Repository;

use App\Entity\PromiseCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PromiseCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PromiseCategory::class);
    }

    public function getAdminChoices() : array
    {
        $choices = [];

        foreach ($this->findBy([], ['name' => 'ASC']) as $category) { /** @var PromiseCategory $category */
            $choices[ $category->getName() ] = $category;
        }

        return $choices;
    }
}

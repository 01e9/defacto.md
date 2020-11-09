<?php

namespace App\Repository;

use App\Entity\BlogCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BlogCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogCategory[]    findAll()
 * @method BlogCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogCategory::class);
    }

    public function getAdminChoices() : array
    {
        $choices = [];

        foreach ($this->findBy([], ['name' => 'ASC']) as $category) { /** @var BlogCategory $category */
            $choices[ $category->getName() ] = $category;
        }

        return $choices;
    }

    public function findWithPosts(): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.posts', 'p')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

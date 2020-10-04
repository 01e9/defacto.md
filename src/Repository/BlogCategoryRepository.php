<?php

namespace App\Repository;

use App\Entity\BlogCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BlogCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogCategory[]    findAll()
 * @method BlogCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogCategoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
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

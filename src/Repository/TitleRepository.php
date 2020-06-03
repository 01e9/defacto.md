<?php

namespace App\Repository;

use App\Entity\Title;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TitleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Title::class);
    }

    public function getAdminChoices() : array
    {
        $choices = [];

        foreach ($this->findBy([], ['name' => 'ASC']) as $entity) {
            $choices[ $entity->getName() ] = $entity;
        }

        return $choices;
    }
}

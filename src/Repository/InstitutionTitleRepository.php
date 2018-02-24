<?php

namespace App\Repository;

use App\Entity\InstitutionTitle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class InstitutionTitleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, InstitutionTitle::class);
    }

    public function getAdminChoices() : array
    {
        $choices = [];

        foreach ($this->findAll() as $institutionTitle) {
            $choices[
                $institutionTitle->getInstitution()->getName()
                . ' / ' .
                $institutionTitle->getTitle()->getName()
            ] = $institutionTitle;
        }

        return $choices;
    }
}

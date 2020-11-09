<?php

namespace App\Repository;

use App\Entity\InstitutionTitle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class InstitutionTitleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InstitutionTitle::class);
    }

    public function getAdminChoices() : array
    {
        $choices = [];

        foreach ($this->findBy([], [/* can't order by relation */]) as $institutionTitle) {
            $choices[
                $institutionTitle->getInstitution()->getName()
                . ' / ' .
                $institutionTitle->getTitle()->getName()
            ] = $institutionTitle;
        }

        return $choices;
    }
}

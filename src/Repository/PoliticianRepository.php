<?php

namespace App\Repository;

use App\Entity\Politician;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;

class PoliticianRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Politician::class);
    }

    public function getAdminChoices() : array
    {
        $choices = [];

        foreach ($this->findAll() as $politician) {
            $choices[
                $politician->getFirstName() . ' ' . $politician->getLastName()
            ] = $politician;
        }

        return $choices;
    }

    public function getAdminList(Request $request)
    {
        return $this->findAll();
    }
}

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

        foreach ($this->findBy([], ['firstName' => 'ASC', 'lastName' => 'ASC']) as $politician) {
            $choices[
                $politician->getFirstName() . ' ' . $politician->getLastName()
            ] = $politician;
        }

        return $choices;
    }

    public function getAdminList(Request $request)
    {
        return $this->findBy([], ['firstName' => 'ASC', 'lastName' => 'ASC']);
    }

    public function hasConnections(string $id) : bool
    {
        return (
            $this->getEntityManager()->getRepository('App:Mandate')->findOneBy(['politician' => $id])
            ||
            $this->getEntityManager()->getRepository('App:ConstituencyCandidate')->findOneBy(['politician' => $id])
        );
    }
}

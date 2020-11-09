<?php

namespace App\Repository;

use App\Entity\Politician;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method Politician|null find($id, $lockMode = null, $lockVersion = null)
 * @method Politician|null findOneBy(array $criteria, array $orderBy = null)
 * @method Politician[]    findAll()
 * @method Politician[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PoliticianRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
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
            $this->getEntityManager()->getRepository('App:Candidate')->findOneBy(['politician' => $id])
        );
    }
}

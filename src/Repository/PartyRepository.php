<?php

namespace App\Repository;

use App\Entity\Party;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method Party|null find($id, $lockMode = null, $lockVersion = null)
 * @method Party|null findOneBy(array $criteria, array $orderBy = null)
 * @method Party[]    findAll()
 * @method Party[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PartyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Party::class);
    }

    public function getAdminChoices() : array
    {
        $choices = [];

        foreach ($this->findBy([], ['name' => 'ASC']) as $party) {
            $choices[ $party->getName() ] = $party;
        }

        return $choices;
    }

    public function getAdminList(Request $request)
    {
        return $this->findBy([], ['name' => 'ASC']);
    }

    public function hasConnections(string $id) : bool
    {
        return (
            !!$this->getEntityManager()->getRepository('App:Candidate')->findOneBy(['party' => $id])
        );
    }
}

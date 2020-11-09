<?php

namespace App\Repository;

use App\Entity\Constituency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method Constituency|null find($id, $lockMode = null, $lockVersion = null)
 * @method Constituency|null findOneBy(array $criteria, array $orderBy = null)
 * @method Constituency[]    findAll()
 * @method Constituency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConstituencyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Constituency::class);
    }

    public function getAdminChoices() : array
    {
        $choices = [];

        foreach ($this->findBy([], ['number' => 'ASC']) as $constituency) {
            $choices[ $constituency->getName() ] = $constituency;
        }

        return $choices;
    }

    public function getAdminList(Request $request)
    {
        return $this->findBy([], ['number' => 'ASC']);
    }

    public function hasConnections(string $id) : bool
    {
        return (
            !!$this->getEntityManager()->getRepository('App:Mandate')->findOneBy(['constituency' => $id])
        );
    }
}

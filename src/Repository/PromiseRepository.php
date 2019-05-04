<?php

namespace App\Repository;

use App\Data\Filter\PromisesFilterData;
use App\Entity\Promise;
use App\Entity\Mandate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Knp\Component\Pager\Pagination\AbstractPagination;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;

class PromiseRepository extends ServiceEntityRepository
{
    private $paginator;

    public function __construct(RegistryInterface $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Promise::class);

        $this->paginator = $paginator;
    }

    public function getAdminChoicesByMandate(Mandate $mandate) : array
    {
        $choices = [];

        /** @var Promise[] $promises */
        $promises = $this->findBy(
            ['politician' => $mandate->getPolitician(), 'election' => $mandate->getElection()],
            ['madeTime' => 'DESC']
        );
        foreach ($promises as $promise) {
            $choices[ $promise->getName() ] = $promise;
        }

        return $choices;
    }

    public function getAdminListPaginated(Request $request, ?PromisesFilterData $filterData): AbstractPagination
    {
        $query = $this->createQueryBuilder('p')->orderBy('p.madeTime', 'DESC');

        if ($filterData) {
            if ($filterData->code) {
                $query
                    ->andWhere('p.code LIKE :code')
                    ->setParameter('code', addcslashes($filterData->code, "%_") . '%');
            }
            if ($filterData->politician) {
                $query
                    ->andWhere('p.politician = :politician')
                    ->setParameter('politician', $filterData->politician);
            }
            if ($filterData->election) {
                $query
                    ->andWhere('p.election = :election')
                    ->setParameter('election', $filterData->election);
            }
        }

        return $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1), // fixme: hardcode
            10 // fixme: hardcode
        );
    }

    public function hasConnections(string $id) : bool
    {
        return (
            !!$this->getEntityManager()->getRepository('App:PromiseUpdate')->findOneBy(['promise' => $id])
        );
    }
}

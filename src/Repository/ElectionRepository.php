<?php

namespace App\Repository;

use App\Consts;
use App\Entity\Election;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Election|null find($id, $lockMode = null, $lockVersion = null)
 * @method Election|null findOneBy(array $criteria, array $orderBy = null)
 * @method Election[]    findAll()
 * @method Election[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ElectionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Election::class);
    }

    public function getAdminChoices() : array
    {
        $choices = [];

        foreach ($this->findBy([], ['date' => 'DESC']) as $election) {
            $choices[
                $election->getDate()->format(Consts::DATE_FORMAT_PHP) . ' | ' . $election->getName()
            ] = $election;
        }

        return $choices;
    }

    public function findWithSubElectionsIds(Election $election): array
    {
        $parentElectionId = $election->getParent()
            ? $election->getParent()->getId()
            : $election->getId();

        $childElectionIds = array_column(
            $this->createQueryBuilder('e')
                ->where('e.parent = :parent_election')
                ->setParameter('parent_election', $parentElectionId)
                ->getQuery()
                ->getResult(AbstractQuery::HYDRATE_ARRAY),
            'id'
        );

        return array_unique(array_merge([$parentElectionId], $childElectionIds));
    }
}

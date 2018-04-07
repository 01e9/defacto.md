<?php

namespace App\Repository;

use App\Entity\InstitutionTitle;
use App\Entity\Mandate;
use App\Entity\Power;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;

class MandateRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Mandate::class);
    }

    public function getAdminChoices() : array
    {
        $choices = [];

        foreach ($this->findAll() as $mandate) { /** @var Mandate $mandate */
            $choices[ $mandate->getChoiceName() ] = $mandate;
        }

        return $choices;
    }

    public function getAdminList(Request $request)
    {
        return $this->findAll();
    }

    public function getLatestByInstitutionTitle(InstitutionTitle $institutionTitle) : ?Mandate
    {
        return $this->createQueryBuilder('m')
            ->where('m.institutionTitle = :institutionTitle')
            ->orderBy('m.beginDate', 'desc')
            ->setMaxResults(1)
            ->setParameter('institutionTitle', $institutionTitle)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getPromiseStatistics(Mandate $mandate) : array
    {
        $statisticsWithStatus = $this->createQueryBuilder('m')
            ->select('COUNT(s.id) AS count', 's AS status')
            ->innerJoin('App:Promise', 'p', Expr\Join::WITH, 'p.published = true AND p.mandate = m.id')
            ->innerJoin('App:Status', 's', Expr\Join::WITH,'s.id = p.status')
            ->where('m.id = :mandate')
            ->orderBy('s.effect','desc')
            ->groupBy('s.id')
            ->setParameter('mandate', $mandate)
            ->getQuery()
            ->getArrayResult();

        $statisticsWithoutStatus = $this->createQueryBuilder('m')
            ->select('COUNT(p.id) AS count')
            ->innerJoin(
                'App:Promise', 'p', Expr\Join::WITH,
                'p.published = true AND p.status IS NULL AND p.mandate = m.id'
            )
            ->where('m.id = :mandate')
            ->setParameter('mandate', $mandate)
            ->getQuery()
            ->getArrayResult();

        return array_merge($statisticsWithStatus, $statisticsWithoutStatus);
    }

    public function getPowersStatistics(Mandate $mandate) : array
    {
        /** @var Power[] $powers */
        $powers = $mandate->getInstitutionTitle()->getTitle()->getPowers();

        $statistics = [
            'count_all' => count($powers),
            'count_used' => 0,
            'powers' => (function () use (&$powers) {
                $byPower = [];
                foreach ($powers as $power) {
                    $byPower[ $power->getId() ] = [
                        'count' => 0,
                        'power' => $power,
                    ];
                }
                return $byPower;
            })(),
        ];

        $powerStatistics = $this->createQueryBuilder('m')
            ->select('p.id, COUNT(p.id) as count')
            ->innerJoin('App:Action', 'a', Expr\Join::WITH,
                'a.mandate = m AND a.published = true'
            )
            ->innerJoin('a.usedPowers', 'p')
            ->groupBy('p.id')
            ->getQuery()
            ->getArrayResult();

        foreach ($powerStatistics as $powerStatistic) {
            $statistics['powers'][ $powerStatistic['id'] ]['count'] = $powerStatistic['count'];
        }

        $statistics['count_used'] = array_reduce(
            $statistics['powers'],
            function($carry, $item) { return $item['count'] ? ++$carry : $carry; },
            0
        );

        return $statistics;
    }
}

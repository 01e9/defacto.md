<?php

namespace App\Repository;

use App\Entity\Election;
use App\Entity\InstitutionTitle;
use App\Entity\Mandate;
use App\Entity\Power;
use App\Repository\Vo\MandateStatisticsVo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
/**
 * @method Mandate|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mandate|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mandate[]    findAll()
 * @method Mandate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MandateRepository extends ServiceEntityRepository
{
    private SettingRepository $settingRepository;
    private ElectionRepository $electionRepository;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Mandate::class);
    }

    /**
     * @required
     */
    public function setRequirements(
        SettingRepository $settingRepository,
        ElectionRepository $electionRepository
    )
    {
        $this->settingRepository = $settingRepository;
        $this->electionRepository = $electionRepository;
    }

    public function getAdminChoices() : array
    {
        $choices = [];

        foreach ($this->findBy([], ['beginDate' => 'DESC']) as $mandate) { /** @var Mandate $mandate */
            $choices[ $mandate->getChoiceName() ] = $mandate;
        }

        return $choices;
    }

    public function getAdminList(Request $request)
    {
        return $this->findBy([], ['beginDate' => 'DESC']);
    }

    public function getLatestByInstitutionTitle(InstitutionTitle $institutionTitle) : ?Mandate
    {
        return $this->createQueryBuilder('m')
            ->where('m.politician IS NOT NULL AND m.institutionTitle = :institutionTitle')
            ->orderBy('m.beginDate', 'DESC')
            ->setMaxResults(1)
            ->setParameter('institutionTitle', $institutionTitle)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getPromiseStatistics(Mandate $mandate) : array
    {
        $statistics = $this->createQueryBuilder('m')
            ->select('COUNT(s.id) AS count', 's AS status')
            ->innerJoin(
                'App:Promise', 'p', Expr\Join::WITH,
                'p.published = true AND p.politician = m.politician AND p.election = m.election'
            )
            ->innerJoin('App:Status', 's', Expr\Join::WITH,'s.id = p.status')
            ->where('m.id = :mandate')
            ->orderBy('s.effect','DESC')
            ->groupBy('s.id')
            ->setParameter('mandate', $mandate)
            ->getQuery()
            ->getArrayResult();

        $statisticsWithoutStatus = $this->createQueryBuilder('m')
            ->select('COUNT(p.id) AS count')
            ->innerJoin(
                'App:Promise', 'p', Expr\Join::WITH,
                'p.published = true AND p.status IS NULL AND p.politician = m.politician AND p.election = m.election'
            )
            ->where('m.id = :mandate')
            ->setParameter('mandate', $mandate)
            ->getQuery()
            ->getArrayResult();

        if ($statisticsWithoutStatus[0]['count']) {
            $statistics = array_merge($statistics, $statisticsWithoutStatus);
        }

        return $statistics;
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
            ->innerJoin('App:PromiseAction', 'a', Expr\Join::WITH,
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

    public function findCompetencePointsRank(Mandate $mandate): int
    {
        if (!$mandate->getElection()->isCompetenceUseTracked()) {
            return 0;
        }

        $elections = $this->getEntityManager()->getRepository(Election::class)
            ->findWithSubElections($mandate->getElection());

        try {
            $count = $this->createQueryBuilder('m')
                ->select('COUNT(DISTINCT(m.competenceUsesPoints))')
                ->where('m.competenceUsesPoints > :points')
                ->andWhere('m.election IN (:elections)')
                ->setParameter('points', $mandate->getCompetenceUsesPoints())
                ->setParameter('elections', $elections)
                ->getQuery()
                ->getSingleResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);

            return 1 + $count;
        } catch (NoResultException $exception) {
            return 1;
        }
    }

    public function findOneBySlugs(string $electionSlug, string $politicianSlug): ?Mandate
    {
        return $this->createQueryBuilder('m')
            ->join('m.election', 'e')
            ->join('m.politician', 'p')
            ->where('e.slug = :electionSlug')
            ->andWhere('p.slug = :politicianSlug')
            ->setMaxResults(1)
            ->setParameter('electionSlug', $electionSlug)
            ->setParameter('politicianSlug', $politicianSlug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getCurrentPresidentStatistics(): ?MandateStatisticsVo
    {
        $settingId = SettingRepository::PRESIDENT_INSTITUTION_TITLE_ID;

        /** @var InstitutionTitle $institutionTitle */
        $institutionTitle = $this->settingRepository->get($settingId);
        if (!$institutionTitle) {
            return null;
        }

        /** @var Mandate $mandate */
        $mandate = $this->getLatestByInstitutionTitle($institutionTitle);
        if (!$mandate) {
            return null;
        }

        $statistics = new MandateStatisticsVo();
        $statistics->mandate = $mandate;
        $statistics->promiseStatistics = $this->getPromiseStatistics($mandate);

        return $statistics;
    }

    /**
     * @return Mandate[]
     */
    public function getTopRanked(Election $election): array
    {
        $elections = $this->electionRepository->findWithSubElections($election);

        return $this->createQueryBuilder('m')
            ->where('m.ceasingDate IS NULL')
            ->andWhere('m.election IN (:elections)')
            ->orderBy('m.competenceUsesPoints', 'DESC')
            ->setMaxResults(5 /* todo: constant */)
            ->setParameter('elections', $elections)
            ->getQuery()
            ->getResult();
    }

    public function hasConnections(string $id) : bool
    {
        return false;
    }
}

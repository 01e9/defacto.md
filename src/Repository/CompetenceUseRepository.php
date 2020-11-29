<?php

namespace App\Repository;

use App\Consts;
use App\Entity\CompetenceUse;
use App\Entity\Mandate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Platforms\OraclePlatform;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CompetenceUse|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompetenceUse|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompetenceUse[]    findAll()
 * @method CompetenceUse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompetenceUseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompetenceUse::class);
    }

    public function findUseCountByMonth(Mandate $mandate): array
    {
        $result = [];
        foreach (
            $this->createQueryBuilder('cu')
                ->select('COUNT(1) AS useCount, DATE_TRUNC(\'month\', cu.useDate) AS useMonth')
                ->where('cu.mandate = :mandate')
                ->setParameter('mandate', $mandate)
                ->orderBy('useMonth', 'ASC')
                ->groupBy('useMonth')
                ->getQuery()
                ->getResult()
            as $row
        ) {
            $date = \DateTime::createFromFormat(OraclePlatform::getDateTimeTzFormatString(), $row['useMonth']);
            $key = $date->format(Consts::DATE_FORMAT_COMPETENCE_USE_MONTH);

            $result[ $key ] = $row['useCount'];
        }

        $beginDate = (clone $mandate->getBeginDate())->modify('first day of this month');
        $endDate = (clone $mandate->getEndDate())->modify('first day of this month');
        $currentDate = (new \DateTime('now'))->modify('first day of this month');
        if ($endDate > $currentDate) {
            $endDate = $currentDate;
        }

        $paddedResult = [];
        for (
            $date = clone $beginDate;
            $date <= $endDate;
            $date->modify('first day of next month')
        ) {
            $key = $date->format(Consts::DATE_FORMAT_COMPETENCE_USE_MONTH);

            if (isset($result[ $key ])) {
                $paddedResult[ $key ] = $result[ $key ];
            } else {
                $paddedResult[ $key ] = 0;
            }
        }

        return $paddedResult;
    }
}

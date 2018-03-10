<?php

namespace App\Repository;

use App\Entity\InstitutionTitle;
use App\Entity\Mandate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;

class MandateRepository extends ServiceEntityRepository
{
    private $dateFormat = 'd.m.Y';

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Mandate::class);
    }

    public function getAdminChoices() : array
    {
        $choices = [];

        foreach ($this->findAll() as $mandate) { /** @var Mandate $mandate */
            $choices[
                $mandate->getPolitician()->getFirstName() . ' ' . $mandate->getPolitician()->getLastName()
                . ' / ' .
                (
                    $mandate->getBeginDate()->format($this->dateFormat)
                    . ' - ' .
                    $mandate->getEndDate()->format($this->dateFormat)
                )
                . ' / ' .
                $mandate->getInstitutionTitle()->getTitle()->getName()
            ] = $mandate;
        }

        return $choices;
    }

    public function getAdminList(Request $request)
    {
        return $this->findAll();
    }

    public function getLatestByInstitutionTitle(InstitutionTitle $institutionTitle)
    {
        return $this->createQueryBuilder('m')
            ->where('m.institutionTitle = :institutionTitle')
            ->orderBy('m.beginDate', 'desc')
            ->setMaxResults(1)
            ->setParameter('institutionTitle', $institutionTitle)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getPromiseStatistics(Mandate $mandate)
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
}

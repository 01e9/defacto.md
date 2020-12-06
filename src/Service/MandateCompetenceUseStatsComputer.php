<?php

namespace App\Service;

use App\Consts;
use App\Entity\CompetenceCategory;
use App\Entity\CompetenceUse;
use App\Entity\Mandate;
use App\Entity\MandateCompetenceCategoryStats;
use App\Filter\MandateFilter;
use App\Repository\CompetenceCategoryRepository;
use App\Repository\CompetenceRepository;
use App\Repository\MandateCompetenceCategoryStatsRepository;
use App\Vo\MandateCompetenceUseStatsVo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr;

class MandateCompetenceUseStatsComputer
{
    private CompetenceRepository $competenceRepository;
    private CompetenceCategoryRepository $competenceCategoryRepository;
    private MandateCompetenceCategoryStatsRepository $mandateCompetenceCategoryStatsRepository;

    public function __construct(
        CompetenceRepository $competenceRepository,
        CompetenceCategoryRepository $competenceCategoryRepository,
        MandateCompetenceCategoryStatsRepository $mandateCompetenceCategoryStatsRepository
    ) {
        $this->competenceRepository = $competenceRepository;
        $this->competenceCategoryRepository = $competenceCategoryRepository;
        $this->mandateCompetenceCategoryStatsRepository = $mandateCompetenceCategoryStatsRepository;
    }

    public function compute(Mandate $mandate, ?MandateFilter $filter = null): MandateCompetenceUseStatsVo
    {
        $filter = $filter ?: new MandateFilter();
        $vo = new MandateCompetenceUseStatsVo();

        $getCategoriesStats = function (bool $isMultiplied) use ($mandate, $filter) {
            $query = $this->competenceRepository
                ->createQueryBuilder('c')
                ->select('COUNT(c.id) as use_count, SUM(c.points) as use_points, ca.id as category_id')
                ->innerJoin(
                    CompetenceUse::class, 'cu', Expr\Join::WITH,
                    'cu.competence = c AND cu.mandate = :mandate AND cu.isMultiplied = :is_multiplied'
                )
                ->innerJoin('c.category', 'ca')
                ->groupBy('ca.id')
                ->setParameter('mandate', $mandate)
                ->setParameter('is_multiplied', $isMultiplied)
            ;

            if ($filter->fromDate) {
                $query->andWhere('cu.useDate >= :fromDate')->setParameter('fromDate', $filter->fromDate);
            }
            if ($filter->toDate) {
                $query->andWhere('cu.useDate <= :toDate')->setParameter('toDate', $filter->toDate);
            }
            if ($filter->categories) {
                $query->andWhere('c.category IN(:categories)')->setParameter('categories', $filter->categories);
            }

            return $query->getQuery()->getResult();
        };

        $categoriesStats = [
            'regular' => $getCategoriesStats(false),
            'multiplied' => $getCategoriesStats(true),
        ];

        if (!count($categoriesStats['regular']) && !count($categoriesStats['multiplied'])) {
            return $vo;
        }

        $categories = [];
        foreach (
            $this->competenceCategoryRepository
                ->createQueryBuilder('cc')
                ->select('cc')
                ->where('cc.id IN (:ids)')
                ->setParameter('ids', array_unique(array_merge(
                    array_column($categoriesStats['regular'], 'category_id'),
                    array_column($categoriesStats['multiplied'], 'category_id'),
                )))
                ->getQuery()
                ->getResult()
            as $category /** @var CompetenceCategory $category */
        ) {
            $categories[ $category->getId() ] = $category;
        }

        $categoryIdToStats = [];
        foreach (
            [
                [false, $categoriesStats['regular']],
                [true, $categoriesStats['multiplied']],
            ]
            as $pair
        ) {
            list($isMultiplied, $stats) = $pair;

            foreach ($stats as $categoryStatsData) {
                $categoryId = $categoryStatsData['category_id'];

                if (!isset($categoryIdToStats[ $categoryId ])) {
                    $categoryStats = new MandateCompetenceCategoryStats();
                    $categoryStats->setMandate($mandate);
                    $categoryStats->setCompetenceCategory($categories[ $categoryId ]);

                    $categoryIdToStats[ $categoryId ] = $categoryStats;
                }

                $categoryStats = $categoryIdToStats[ $categoryId ];

                $useCount = $categoryStatsData['use_count'];
                $usePoints = $categoryStatsData['use_points'] * ($isMultiplied ? Consts::COMPETENCE_USE_MULTIPLICATION_FACTOR : 1);

                $categoryStats->setCompetenceUsesCount($useCount + $categoryStats->getCompetenceUsesCount());
                $categoryStats->setCompetenceUsesPoints($usePoints + $categoryStats->getCompetenceUsesPoints());

                $vo->useCount += $useCount;
                $vo->usePoints += $usePoints;
            }
        }

        $vo->categoryStats = new ArrayCollection($categoryIdToStats);

        return $vo;
    }
}
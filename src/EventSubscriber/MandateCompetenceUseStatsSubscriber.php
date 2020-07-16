<?php

namespace App\EventSubscriber;

use App\Consts;
use App\Entity\CompetenceCategory;
use App\Entity\CompetenceUse;
use App\Entity\MandateCompetenceCategoryStats;
use App\Event\MandateUpdatedEvent;
use App\Repository\CompetenceCategoryRepository;
use App\Repository\CompetenceRepository;
use App\Repository\MandateCompetenceCategoryStatsRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MandateCompetenceUseStatsSubscriber implements EventSubscriberInterface
{
    private CompetenceRepository $competenceRepository;
    private CompetenceCategoryRepository $competenceCategoryRepository;
    private MandateCompetenceCategoryStatsRepository $mandateCompetenceCategoryStatsRepository;
    private ObjectManager $objectManager;

    public function __construct(
        CompetenceRepository $competenceRepository,
        CompetenceCategoryRepository $competenceCategoryRepository,
        MandateCompetenceCategoryStatsRepository $mandateCompetenceCategoryStatsRepository,
        ObjectManager $objectManager
    ) {
        $this->competenceRepository = $competenceRepository;
        $this->competenceCategoryRepository = $competenceCategoryRepository;
        $this->mandateCompetenceCategoryStatsRepository = $mandateCompetenceCategoryStatsRepository;
        $this->objectManager = $objectManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            MandateUpdatedEvent::class => [
                ['update', 0],
            ]
        ];
    }

    function update(MandateUpdatedEvent $event)
    {
        $mandate = $event->getMandate();

        $this->mandateCompetenceCategoryStatsRepository
            ->createQueryBuilder('s')
            ->delete(MandateCompetenceCategoryStats::class, 's')
            ->where('s.mandate = :mandate')
            ->setParameter('mandate', $mandate)
            ->getQuery()
            ->execute();

        $mandate->setCompetenceUsesCount(0);
        $mandate->setCompetenceUsesPoints(0);
        $this->objectManager->flush();

        if (!$mandate->getElection()->isCompetenceUseTracked()) {
            return;
        }

        $getCategoriesStats = function (bool $isMultiplied) use ($mandate) {
            return $this->competenceRepository
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
                ->getQuery()
                ->getResult();
        };

        $categoriesStats = [
            'regular' => $getCategoriesStats(false),
            'multiplied' => $getCategoriesStats(true),
        ];

        if (!count($categoriesStats['regular']) && !count($categoriesStats['multiplied'])) {
            return;
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

        $totalUseCount = 0;
        $totalUsePoints = 0;
        $categoryToStats = [];

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

                if (!isset($categoryToStats[ $categoryId ])) {
                    $categoryStats = new MandateCompetenceCategoryStats();
                    $categoryStats->setMandate($mandate);
                    $categoryStats->setCompetenceCategory($categories[ $categoryId ]);

                    $categoryToStats[ $categoryId ] = $categoryStats;
                }

                $categoryStats = $categoryToStats[ $categoryId ];

                $useCount = $categoryStatsData['use_count'];
                $usePoints = $categoryStatsData['use_points'] * ($isMultiplied ? Consts::COMPETENCE_USE_MULTIPLICATION_FACTOR : 1);

                $categoryStats->setCompetenceUsesCount($useCount + $categoryStats->getCompetenceUsesCount());
                $categoryStats->setCompetenceUsesPoints($usePoints + $categoryStats->getCompetenceUsesPoints());

                $totalUseCount += $useCount;
                $totalUsePoints += $usePoints;
            }
        }

        array_walk($categoryToStats, function ($categoryStats) {
            $this->objectManager->persist($categoryStats);
        });

        $mandate->setCompetenceUsesCount($totalUseCount);
        $mandate->setCompetenceUsesPoints($totalUsePoints);

        $this->objectManager->flush();
    }
}

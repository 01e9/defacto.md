<?php

namespace App\EventSubscriber;

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

        $categoriesStats = $this->competenceRepository
            ->createQueryBuilder('c')
            ->select('COUNT(c.id) as use_count, ca.id as category_id')
            ->innerJoin(
                CompetenceUse::class, 'cu', Expr\Join::WITH,
                'cu.competence = c AND cu.mandate = :mandate'
            )
            ->innerJoin('c.category', 'ca')
            ->groupBy('ca.id')
            ->setParameter('mandate', $mandate)
            ->getQuery()
            ->getResult();

        if (!count($categoriesStats)) {
            return;
        }

        $categories = [];
        foreach (
            $this->competenceCategoryRepository
                ->createQueryBuilder('cc')
                ->select('cc')
                ->where('cc.id IN (:ids)')
                ->setParameter('ids', array_column($categoriesStats, 'category_id'))
                ->getQuery()
                ->getResult()
            as $category /** @var CompetenceCategory $category */
        ) {
            $categories[ $category->getId() ] = $category;
        }

        $totalUseCount = 0;

        foreach ($categoriesStats as $categoryStatsData) {
            $categoryStats = new MandateCompetenceCategoryStats();
            $categoryStats->setMandate($mandate);
            $categoryStats->setCompetenceCategory($categories[ $categoryStatsData['category_id'] ]);
            $categoryStats->setCompetenceUsesCount($categoryStatsData['use_count']);

            $this->objectManager->persist($categoryStats);

            $totalUseCount += $categoryStatsData['use_count'];
        }

        $mandate->setCompetenceUsesCount($totalUseCount);

        $this->objectManager->flush();
    }
}

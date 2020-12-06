<?php

namespace App\EventSubscriber;

use App\Entity\MandateCompetenceCategoryStats;
use App\Event\MandateUpdatedEvent;
use App\Repository\MandateCompetenceCategoryStatsRepository;
use App\Service\MandateCompetenceUseStatsComputer;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MandateCompetenceUseStatsSubscriber implements EventSubscriberInterface
{
    private MandateCompetenceCategoryStatsRepository $mandateCompetenceCategoryStatsRepository;
    private ObjectManager $objectManager;
    private MandateCompetenceUseStatsComputer $competenceUseStatsComputer;

    public function __construct(
        MandateCompetenceCategoryStatsRepository $mandateCompetenceCategoryStatsRepository,
        ObjectManager $objectManager,
        MandateCompetenceUseStatsComputer $competenceUseStatsComputer
    ) {
        $this->mandateCompetenceCategoryStatsRepository = $mandateCompetenceCategoryStatsRepository;
        $this->objectManager = $objectManager;
        $this->competenceUseStatsComputer = $competenceUseStatsComputer;
    }

    public static function getSubscribedEvents()
    {
        return [
            MandateUpdatedEvent::class => [['update', 0]]
        ];
    }

    function update(MandateUpdatedEvent $event)
    {
        $mandate = $event->getMandate();

        // reset
        {
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
        }

        if (!$mandate->getElection()->isCompetenceUseTracked()) {
            return;
        }

        $vo = $this->competenceUseStatsComputer->compute($mandate);

        // update
        {
            foreach ($vo->categoryStats as $categoryStats) {
                $this->objectManager->persist($categoryStats);
            }

            $mandate->setCompetenceUsesCount($vo->useCount);
            $mandate->setCompetenceUsesPoints($vo->usePoints);

            $this->objectManager->flush();
        }
    }
}

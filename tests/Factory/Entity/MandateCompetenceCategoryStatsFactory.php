<?php

namespace App\Tests\Factory\Entity;

use App\Entity\MandateCompetenceCategoryStats;
use App\Tests\TestContainer;

class MandateCompetenceCategoryStatsFactory
{
    const COMPETENCE_USES_COUNT = 'competenceUsesCount';
    const COMPETENCE_USES_POINTS = 'competenceUsesPoints';
    const MANDATE = 'mandate';
    const COMPETENCE_CATEGORY = 'competenceCategory';

    public static function create(TestContainer $container, array $params = []): MandateCompetenceCategoryStats
    {
        $entity = new MandateCompetenceCategoryStats();
        $faker = $container->faker();

        $entity->setCompetenceUsesCount($params[self::COMPETENCE_USES_COUNT] ?? $entity->getCompetenceUsesCount());
        $entity->setCompetenceUsesPoints($params[self::COMPETENCE_USES_POINTS] ?? $entity->getCompetenceUsesPoints());
        $entity->setMandate($params[self::MANDATE] ?? MandateFactory::create($container));
        $entity->setCompetenceCategory($params[self::COMPETENCE_CATEGORY] ?? CompetenceCategoryFactory::create($container));

        return $entity;
    }
}
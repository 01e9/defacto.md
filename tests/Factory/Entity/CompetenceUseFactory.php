<?php

namespace App\Tests\Factory\Entity;

use App\Entity\CompetenceUse;
use App\Tests\TestContainer;

class CompetenceUseFactory
{
    const DESCRIPTION = 'description';
    const MANDATE = 'mandate';
    const COMPETENCE = 'competence';
    const USE_DATE = 'useDate';
    const SOURCE_LINK = 'sourceLink';
    const IS_MULTIPLIED = 'isMultiplied';

    public static function create(TestContainer $container, array $params = []): CompetenceUse
    {
        $entity = new CompetenceUse();
        $faker = $container->faker();

        $entity->setDescription($params[self::DESCRIPTION] ?? null);
        $entity->setMandate($params[self::MANDATE] ?? null /* todo */);
        $entity->setCompetence($params[self::COMPETENCE] ?? null /* todo */);
        $entity->setUseDate($params[self::USE_DATE] ?? $faker->dateTimeBetween($entity->getMandate()->getBeginDate()));
        $entity->setSourceLink($params[self::SOURCE_LINK] ?? null);
        $entity->setIsMultiplied($params[self::IS_MULTIPLIED] ?? $entity->isMultiplied());

        return $entity;
    }
}
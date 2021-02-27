<?php

namespace App\Tests\Factory\Entity;

use App\Entity\Election;
use App\Tests\TestContainer;

class ElectionFactory
{
    const PARENT = 'parent';
    const SLUG = 'slug';
    const NAME = 'name';
    const THE_NAME = 'theName';
    const THE_ELECTED_NAME = 'theElectedName';
    const DATE = 'date';
    const IS_COMPETENCE_USE_TRACKED = 'isCompetenceUseTracked';

    public static function create(TestContainer $container, array $params = []): Election
    {
        $entity = new Election();
        $faker = $container->faker();

        $entity->setSlug($params[self::SLUG] ?? $faker->unique()->slug);
        $entity->setName($params[self::NAME] ?? $faker->words(3, true));
        $entity->setTheName($params[self::THE_NAME] ?? "The {$entity->getName()}");
        $entity->setTheElectedName($params[self::THE_ELECTED_NAME] ?? "The elected {$entity->getTheElectedName()}");
        $entity->setIsCompetenceUseTracked($params[self::IS_COMPETENCE_USE_TRACKED] ?? $entity->isCompetenceUseTracked());

        return $entity;
    }
}
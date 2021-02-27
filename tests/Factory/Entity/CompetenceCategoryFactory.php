<?php

namespace App\Tests\Factory\Entity;

use App\Entity\CompetenceCategory;
use App\Tests\TestContainer;

class CompetenceCategoryFactory
{
    const SLUG = 'slug';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const PARENT = 'parent';

    public static function create(TestContainer $container, array $params = []): CompetenceCategory
    {
        $entity = new CompetenceCategory();
        $faker = $container->faker();

        $entity->setSlug($params[self::SLUG] ?? $faker->unique()->slug);
        $entity->setName($params[self::NAME] ?? $faker->words(3, true));
        $entity->setDescription($params[self::DESCRIPTION] ?? null);
        $entity->setParent($params[self::PARENT] ?? null);

        return $entity;
    }
}
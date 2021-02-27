<?php

namespace App\Tests\Factory\Entity;

use App\Entity\Institution;
use App\Tests\TestContainer;

class InstitutionFactory
{
    const SLUG = 'slug';
    const NAME = 'name';

    public static function create(TestContainer $container, array $params = []): Institution
    {
        $entity = new Institution();
        $faker = $container->faker();

        $entity->setSlug($params[self::SLUG] ?? $faker->unique()->slug);
        $entity->setName($params[self::NAME] ?? $faker->words(3, true));

        return $entity;
    }
}
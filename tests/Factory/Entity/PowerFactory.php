<?php

namespace App\Tests\Factory\Entity;

use App\Entity\Power;
use App\Tests\TestContainer;

class PowerFactory
{
    const SLUG = 'slug';
    const NAME = 'name';

    public static function create(TestContainer $container, array $params = []): Power
    {
        $entity = new Power();
        $faker = $container->faker();

        $entity->setSlug($params[self::SLUG] ?? $faker->unique()->slug);
        $entity->setName($params[self::NAME] ?? $faker->words(3, true));

        return $entity;
    }
}
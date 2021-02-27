<?php

namespace App\Tests\Factory\Entity;

use App\Entity\Problem;
use App\Tests\TestContainer;

class ProblemFactory
{
    const SLUG = 'slug';
    const NAME = 'name';

    public static function create(TestContainer $container, array $params = []): Problem
    {
        $entity = new Problem();
        $faker = $container->faker();

        $entity->setSlug($params[self::SLUG] ?? $faker->unique()->slug);
        $entity->setName($params[self::NAME] ?? $faker->words(3, true));

        return $entity;
    }
}
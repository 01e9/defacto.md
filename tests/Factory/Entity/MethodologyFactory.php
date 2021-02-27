<?php

namespace App\Tests\Factory\Entity;

use App\Entity\Methodology;
use App\Tests\TestContainer;

class MethodologyFactory
{
    const TITLE = 'title';
    const SLUG = 'slug';
    const CONTENT = 'content';

    public static function create(TestContainer $container, array $params = []): Methodology
    {
        $entity = new Methodology();
        $faker = $container->faker();

        $entity->setTitle($params[self::TITLE] ?? $faker->words(3, true));
        $entity->setSlug($params[self::SLUG] ?? $faker->unique()->slug);
        $entity->setContent($params[self::CONTENT] ?? $faker->text(50000));

        return $entity;
    }
}
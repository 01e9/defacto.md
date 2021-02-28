<?php

namespace App\Tests\Factory\Entity;

use App\Entity\Status;
use App\Tests\TestContainer;

class StatusFactory
{
    const SLUG = 'slug';
    const NAME = 'name';
    const NAME_PLURAL = 'namePlural';
    const DESCRIPTION = 'description';
    const EFFECT = 'effect';
    const COLOR = 'color';

    public static function create(TestContainer $container, array $params = []): Status
    {
        $entity = new Status();
        $faker = $container->faker();

        $entity->setSlug($params[self::SLUG] ?? $faker->unique()->slug);
        $entity->setName($params[self::NAME] ?? $faker->words(3, true));
        $entity->setNamePlural($params[self::NAME_PLURAL] ?? $faker->words(3, true));
        $entity->setDescription($params[self::DESCRIPTION] ?? null);
        $entity->setEffect($params[self::EFFECT] ?? $faker->unique()->numberBetween(-1000, 1000));
        $entity->setColor($params[self::COLOR] ?? $faker->colorName);

        return $entity;
    }
}

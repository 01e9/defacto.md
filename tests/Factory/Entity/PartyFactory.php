<?php

namespace App\Tests\Factory\Entity;

use App\Entity\Party;
use App\Tests\TestContainer;

class PartyFactory
{
    const SLUG = 'slug';
    const NAME = 'name';
    const LOGO = 'logo';

    public static function create(TestContainer $container, array $params = []): Party
    {
        $entity = new Party();
        $faker = $container->faker();

        $entity->setSlug($params[self::SLUG] ?? $faker->unique()->slug);
        $entity->setName($params[self::NAME] ?? $faker->words(3, true));
        $entity->setLogo($params[self::LOGO] ?? null);

        return $entity;
    }
}
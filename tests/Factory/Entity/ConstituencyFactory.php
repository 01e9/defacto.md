<?php

namespace App\Tests\Factory\Entity;

use App\Entity\Constituency;
use App\Tests\TestContainer;

class ConstituencyFactory
{
    const SLUG = 'slug';
    const NAME = 'name';
    const LINK = 'link';
    const MAP = 'map';
    const NUMBER = 'number';

    public static function create(TestContainer $container, array $params = []): Constituency
    {
        $entity = new Constituency();
        $faker = $container->faker();

        $entity->setSlug($params[self::SLUG] ?? $faker->unique()->slug);
        $entity->setName($params[self::NAME] ?? $faker->words(3, true));
        $entity->setLink($params[self::LINK] ?? null);
        $entity->setMap($params[self::MAP] ?? null);
        $entity->setNumber($params[self::NUMBER] ?? $faker->unique()->numberBetween());

        return $entity;
    }
}
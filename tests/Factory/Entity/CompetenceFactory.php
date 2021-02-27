<?php

namespace App\Tests\Factory\Entity;

use App\Entity\Competence;
use App\Tests\TestContainer;

class CompetenceFactory
{
    const SLUG = 'slug';
    const NAME = 'name';
    const TITLE = 'title';
    const CATEGORY = 'category';
    const CODE = 'code';
    const POINTS = 'points';

    public static function create(TestContainer $container, array $params = []): Competence
    {
        $entity = new Competence();
        $faker = $container->faker();

        $entity->setSlug($params[self::SLUG] ?? $faker->unique()->slug);
        $entity->setName($params[self::NAME] ?? $faker->unique()->name);
        $entity->setTitle($params[self::TITLE] ?? null /* todo */);
        $entity->setCategory($params[self::CATEGORY] ?? null /* todo */);
        $entity->setCode($params[self::CODE] ?? substr($faker->unique()->md5, 0, 10));
        $entity->setPoints($params[self::POINTS] ?? $faker->numberBetween(0, 100));

        return $entity;
    }
}
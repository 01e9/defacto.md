<?php

namespace App\Tests\Factory\Entity;

use App\Entity\BlogCategory;
use App\Tests\TestContainer;

class BlogCategoryFactory
{
    const SLUG = 'slug';
    const NAME = 'name';

    public static function create(TestContainer $container, array $params = []): BlogCategory
    {
        $entity = new BlogCategory();
        $faker = $container->faker();

        $entity->setSlug($params[self::SLUG] ?? $faker->unique()->slug);
        $entity->setName($params[self::NAME] ?? $faker->words(3, true));

        return $entity;
    }
}
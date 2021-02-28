<?php

namespace App\Tests\Factory\Entity;

use App\Entity\PromiseActionSource;
use App\Tests\TestContainer;

class PromiseActionSourceFactory
{
    const NAME = 'name';
    const LINK = 'link';
    const ACTION = 'action';

    public static function create(TestContainer $container, array $params = []): PromiseActionSource
    {
        $entity = new PromiseActionSource();
        $faker = $container->faker();

        $entity->setName($params[self::NAME] ?? $faker->words(3, true));
        $entity->setLink($params[self::LINK] ?? $faker->url);
        $entity->setAction($params[self::ACTION] ?? PromiseActionFactory::create($container));

        return $entity;
    }
}
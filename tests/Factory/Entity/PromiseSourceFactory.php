<?php

namespace App\Tests\Factory\Entity;

use App\Entity\PromiseSource;
use App\Tests\TestContainer;

class PromiseSourceFactory
{
    const NAME = 'name';
    const LINK = 'link';
    const PROMISE = 'promise';

    public static function create(TestContainer $container, array $params = []): PromiseSource
    {
        $entity = new PromiseSource();
        $faker = $container->faker();

        $entity->setName($params[self::NAME] ?? $faker->words(3, true));
        $entity->setLink($params[self::LINK] ?? $faker->url);
        $entity->setPromise($params[self::PROMISE] ?? PromiseFactory::create($container));

        return $entity;
    }
}
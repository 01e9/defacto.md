<?php

namespace App\Tests\Factory\Entity;

use App\Entity\PromiseUpdate;
use App\Tests\TestContainer;

class PromiseUpdateFactory
{
    const PROMISE = 'promise';
    const ACTION = 'action';
    const STATUS = 'status';

    public static function create(TestContainer $container, array $params = []): PromiseUpdate
    {
        $entity = new PromiseUpdate();
        $faker = $container->faker();

        $entity->setPromise($params[self::PROMISE] ?? PromiseFactory::create($container));
        $entity->setAction($params[self::ACTION] ?? PromiseActionFactory::create($container));
        $entity->setStatus($params[self::STATUS] ?? null /* todo */);

        return $entity;
    }
}
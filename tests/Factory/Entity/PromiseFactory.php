<?php

namespace App\Tests\Factory\Entity;

use App\Entity\Promise;
use App\Tests\TestContainer;

class PromiseFactory
{
    const SLUG = 'slug';
    const NAME = 'name';
    const ELECTION = 'election';
    const POLITICIAN = 'politician';
    const STATUS = 'status';
    const MADE_TIME = 'madeTime';
    const CODE = 'code';
    const DESCRIPTION = 'description';
    const PUBLISHED = 'published';
    const HAS_PREROGATIVES = 'hasPrerogatives';

    public static function create(TestContainer $container, array $params = []): Promise
    {
        $entity = new Promise();
        $faker = $container->faker();

        $entity->setSlug($params[self::SLUG] ?? $faker->unique()->slug);
        $entity->setName($params[self::NAME] ?? $faker->words(3, true));
        $entity->setElection($params[self::ELECTION] ?? ElectionFactory::create($container));
        $entity->setPolitician($params[self::POLITICIAN] ?? PoliticianFactory::create($container));
        $entity->setStatus($params[self::STATUS] ?? null /* todo */);
        $entity->setMadeTime($params[self::MADE_TIME] ?? $faker->dateTime($entity->getElection()->getDate()));
        $entity->setCode($params[self::CODE] ?? substr($faker->unique()->md5, 0, 10));
        $entity->setDescription($params[self::DESCRIPTION] ?? null);
        $entity->setPublished($params[self::PUBLISHED] ?? $entity->getPublished());
        $entity->setHasPrerogatives($params[self::HAS_PREROGATIVES] ?? $entity->getHasPrerogatives());

        return $entity;
    }
}
<?php

namespace App\Tests\Factory\Entity;

use App\Entity\PromiseAction;
use App\Tests\TestContainer;

class PromiseActionFactory
{
    const SLUG = 'slug';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const OCCURRED_TIME = 'occurredTime';
    const MANDATE = 'mandate';
    const PUBLISHED = 'published';

    public static function create(TestContainer $container, array $params = []): PromiseAction
    {
        $entity = new PromiseAction();
        $faker = $container->faker();

        $entity->setSlug($params[self::SLUG] ?? $faker->unique()->slug);
        $entity->setName($params[self::NAME] ?? $faker->words(3, true));
        $entity->setDescription($params[self::DESCRIPTION] ?? null);
        $entity->setMandate($params[self::MANDATE] ?? MandateFactory::create($container));
        $entity->setOccurredTime($params[self::OCCURRED_TIME] ?? $faker->dateTimeBetween($entity->getMandate()->getBeginDate(), '+100 years'));
        $entity->setPublished($params[self::PUBLISHED] ?? $entity->getPublished());

        return $entity;
    }
}
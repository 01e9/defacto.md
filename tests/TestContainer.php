<?php

declare(strict_types=1);

namespace App\Tests;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;

class TestContainer
{
    private ObjectManager $manager;
    private Faker $faker;

    public function __construct(ObjectManager $manager, ?Faker $faker = null)
    {
        $this->manager = $manager;
        $this->faker = $faker ?: FakerFactory::create();
    }

    public function manager(): ObjectManager
    {
        return $this->manager;
    }

    public function faker(): Faker
    {
        return $this->faker;
    }

    public function repository(string $class): ObjectRepository
    {
        return $this->manager()->getRepository($class);
    }

    public function flushManager()
    {
        $this->manager()->flush();
        $this->manager()->clear();

        $configuration = $this->manager()->getConfiguration();
        $configuration->getQueryCacheImpl()->flushAll();
        $configuration->getResultCacheImpl()->flushAll();
    }
}

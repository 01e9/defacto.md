<?php

namespace App\Tests\Factory\Entity;

use App\Entity\Title;
use App\Tests\TestContainer;

class TitleFactory
{
    const SLUG = 'slug';
    const NAME = 'name';
    const NAME_FEMALE = 'nameFemale';
    const THE_NAME = 'theName';
    const THE_NAME_FEMALE = 'theNameFemale';

    public static function create(TestContainer $container, array $params = []): Title
    {
        $entity = new Title();
        $faker = $container->faker();

        $entity->setSlug($params[self::SLUG] ?? $faker->unique()->slug);
        $entity->setName($params[self::NAME] ?? $faker->words(3, true));
        $entity->setNameFemale($params[self::NAME_FEMALE] ?? $faker->words(3, true));
        $entity->setTheName($params[self::THE_NAME] ?? "The {$entity->getName()}");
        $entity->setTheNameFemale($params[self::THE_NAME_FEMALE] ?? "The {$entity->getNameFemale()}");

        return $entity;
    }
}

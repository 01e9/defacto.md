<?php

namespace App\Tests\Factory\Entity;

use App\Entity\Politician;
use App\Tests\TestContainer;

class PoliticianFactory
{
    const SLUG = 'slug';
    const FIRST_NAME = 'firstName';
    const LAST_NAME = 'lastName';
    const PHOTO = 'photo';
    const BIRTH_DATE = 'birthDate';
    const STUDIES = 'studies';
    const PROFESSION = 'profession';
    const WEBSITE = 'website';
    const FACEBOOK = 'facebook';
    const EMAIL = 'email';
    const PHONE = 'phone';
    const PREVIOUS_TITLES = 'previousTitles';
    const IS_FEMALE = 'isFemale';

    public static function create(TestContainer $container, array $params = []): Politician
    {
        $entity = new Politician();
        $faker = $container->faker();

        $entity->setSlug($params[self::SLUG] ?? $faker->unique()->slug);
        $entity->setIsFemale($params[self::IS_FEMALE] ?? $entity->isFemale());
        $entity->setFirstName($params[self::FIRST_NAME] ?? $faker->firstName($entity->isFemale() ? 'female' : 'male'));
        $entity->setLastName($params[self::LAST_NAME] ?? $faker->lastName);
        $entity->setPhoto($params[self::PHOTO] ?? null);
        $entity->setBirthDate($params[self::BIRTH_DATE] ?? null);
        $entity->setStudies($params[self::STUDIES] ?? null);
        $entity->setProfession($params[self::PROFESSION] ?? null);
        $entity->setWebsite($params[self::WEBSITE] ?? null);
        $entity->setFacebook($params[self::FACEBOOK] ?? null);
        $entity->setEmail($params[self::EMAIL] ?? null);
        $entity->setPhone($params[self::PHONE] ?? null);
        $entity->setPreviousTitles($params[self::PREVIOUS_TITLES] ?? null);

        return $entity;
    }
}
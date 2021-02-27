<?php

namespace App\Tests\Factory\Entity;

use App\Entity\InstitutionTitle;
use App\Tests\TestContainer;

class InstitutionTitleFactory
{
    const INSTITUTION = 'institution';
    const TITLE = 'title';
    const PREROGATIVES_LINK = 'prerogativesLink';

    public static function create(TestContainer $container, array $params = []): InstitutionTitle
    {
        $entity = new InstitutionTitle();
        $faker = $container->faker();

        $entity->setInstitution($params[self::INSTITUTION] ?? InstitutionFactory::create($container));
        $entity->setTitle($params[self::TITLE] ?? null /* todo */);
        $entity->setPrerogativesLink($params[self::PREROGATIVES_LINK] ?? null);

        return $entity;
    }
}
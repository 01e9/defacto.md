<?php

namespace App\Tests\Factory\Entity;

use App\Entity\User;
use App\Tests\TestContainer;

class UserFactory
{
    const EMAIL = 'email';
    const PASSWORD = 'password';
    const SALT = 'salt';
    const IS_ACTIVE = 'isActive';
    const ROLES = 'roles';

    public static function create(TestContainer $container, array $params = []): User
    {
        $entity = new User();
        $faker = $container->faker();

        $entity->setEmail($params[self::EMAIL] ?? $faker->unique()->email);
        $entity->setPassword($params[self::PASSWORD] ?? $faker->sha1);
        $entity->setSalt($params[self::SALT] ?? $faker->md5);
        $entity->setIsActive($params[self::IS_ACTIVE] ?? $entity->getIsActive());
        $entity->setRoles($params[self::ROLES] ?? $entity->getRoles());

        return $entity;
    }
}
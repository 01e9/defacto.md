<?php

namespace App\Tests\Factory\Entity;

use App\Entity\Setting;
use App\Repository\SettingRepository;
use App\Tests\TestContainer;

class SettingFactory
{
    const ID = 'id';
    const VALUE = 'value';

    public static function create(TestContainer $container, array $params = []): Setting
    {
        $entity = new Setting();
        $faker = $container->faker();

        $entity->setId($params[self::ID] ?? $faker->randomElement([
            SettingRepository::CURRENT_ELECTION_ID,
            SettingRepository::PRESIDENT_INSTITUTION_TITLE_ID
        ]));
        $entity->setValue($params[self::NAME] ?? null);

        return $entity;
    }
}
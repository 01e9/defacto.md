<?php

namespace App\Tests\Factory\Entity;

use App\Entity\Candidate;
use App\Tests\TestContainer;

class CandidateFactory
{
    const POLITICIAN = 'politician';
    const ELECTION = 'election';
    const CONSTITUENCY = 'constituency';
    const PARTY = 'party';
    const REGISTRATION_DATE = 'registration_date';
    const REGISTRATION_NOTE = 'registration_note';
    const REGISTRATION_LINK = 'registration_link';
    const ELECTORAL_PLATFORM = 'electoral_platform';
    const ELECTORAL_PLATFORM_LINK = 'electoral_platform_link';

    public static function create(TestContainer $container, array $params = []): Candidate
    {
        $entity = new Candidate();
        $faker = $container->faker();

        $entity->setPolitician($params[self::POLITICIAN] ?? null /* todo */);
        $entity->setElection($params[self::ELECTION] ?? null /* todo */);
        $entity->setConstituency($params[self::CONSTITUENCY] ?? null);
        $entity->setParty($params[self::PARTY] ?? null);
        $entity->setRegistrationDate($params[self::REGISTRATION_DATE] ?? null);
        $entity->setRegistrationNote($params[self::REGISTRATION_NOTE] ?? null);
        $entity->setRegistrationLink($params[self::REGISTRATION_LINK] ?? null);
        $entity->setElectoralPlatform($params[self::ELECTORAL_PLATFORM] ?? null);
        $entity->setElectoralPlatformLink($params[self::ELECTORAL_PLATFORM_LINK] ?? null);

        return $entity;
    }
}
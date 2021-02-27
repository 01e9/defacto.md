<?php

namespace App\Tests\Factory\Entity;

use App\Entity\CandidateProblemOpinion;
use App\Tests\TestContainer;

class CandidateProblemOpinionFactory
{
    const POLITICIAN = 'politician';
    const ELECTION = 'election';
    const CONSTITUENCY = 'constituency';
    const PROBLEM = 'problem';
    const OPINION = 'opinion';

    public static function create(TestContainer $container, array $params = []): CandidateProblemOpinion
    {
        $entity = new CandidateProblemOpinion();
        $faker = $container->faker();

        $entity->setPolitician($params[self::POLITICIAN] ?? null /* todo */);
        $entity->setElection($params[self::ELECTION] ?? null /* todo */);
        $entity->setConstituency($params[self::CONSTITUENCY] ?? null /* todo */);
        $entity->setProblem($params[self::PROBLEM] ?? null /* todo */);
        $entity->setOpinion($params[self::OPINION] ?? $faker->text(1000));

        return $entity;
    }
}
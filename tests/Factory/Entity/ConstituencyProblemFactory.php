<?php

namespace App\Tests\Factory\Entity;

use App\Entity\ConstituencyProblem;
use App\Tests\TestContainer;

class ConstituencyProblemFactory
{
    const CONSTITUENCY = 'constituency';
    const ELECTION = 'election';
    const PROBLEM = 'problem';
    const RESPONDENTS = 'respondents';
    const PERCENTAGE = 'percentage';
    const TYPE = 'type';
    const QUESTIONNAIRE_EMBED_LINK = 'questionnaireEmbedLink';

    public static function create(TestContainer $container, array $params = []): ConstituencyProblem
    {
        $entity = new ConstituencyProblem();
        $faker = $container->faker();

        $entity->setConstituency($params[self::CONSTITUENCY] ?? ConstituencyFactory::create($container));
        $entity->setElection($params[self::ELECTION] ?? null /* todo */);
        $entity->setProblem($params[self::PROBLEM] ?? null /* todo */);
        $entity->setRespondents($params[self::RESPONDENTS] ?? $faker->numberBetween(1));
        $entity->setPercentage($params[self::PERCENTAGE] ?? ($faker->numberBetween(1, 99) + 0.7));
        $entity->setType($params[self::TYPE] ?? null);
        $entity->setQuestionnaireEmbedLink($params[self::QUESTIONNAIRE_EMBED_LINK] ?? null);

        return $entity;
    }
}
<?php

namespace App\Tests\Factory\Entity;

use App\Entity\Mandate;
use App\Tests\TestContainer;

class MandateFactory
{
    const COMPETENCE_USES_COUNT = 'competenceUsesCount';
    const COMPETENCE_USES_POINTS = 'competenceUsesPoints';
    const BEGIN_DATE = 'beginDate';
    const CEASING_DATE = 'ceasingDate';
    const END_DATE = 'endDate';
    const ELECTION = 'election';
    const CONSTITUENCY = 'constituency';
    const POLITICIAN = 'politician';
    const INSTITUTION_TITLE = 'institutionTitle';
    const VOTES_COUNT = 'votesCount';
    const VOTES_PERCENT = 'votesPercent';
    const DECISION_LINK = 'decisionLink';
    const CEASING_REASON = 'ceasingReason';
    const CEASING_LINK = 'ceasingLink';
    const COMPETENCE_USE_UPDATE_TIME = 'competenceUseUpdateTime';

    public static function create(TestContainer $container, array $params = []): Mandate
    {
        $entity = new Mandate();
        $faker = $container->faker();

        $entity->setCompetenceUsesCount($params[self::COMPETENCE_USES_COUNT] ?? $entity->getCompetenceUsesCount());
        $entity->setCompetenceUsesPoints($params[self::COMPETENCE_USES_POINTS] ?? $entity->getCompetenceUsesPoints());
        $entity->setBeginDate($params[self::BEGIN_DATE] ?? $faker->dateTimeBetween($entity->getElection()->getDate(), (new \DateTime($entity->getElection()->getDate()))->modify("+4 years")));
        $entity->setCeasingDate($params[self::CEASING_DATE] ?? null);
        $entity->setEndDate($params[self::END_DATE] ?? $faker->dateTimeBetween($entity->getBeginDate()));
        $entity->setConstituency($params[self::CONSTITUENCY] ?? ConstituencyFactory::create($container));
        $entity->setPolitician($params[self::POLITICIAN] ?? null /* todo */);
        $entity->setInstitutionTitle($params[self::INSTITUTION_TITLE] ?? InstitutionTitleFactory::create($container));
        $entity->setVotesCount($params[self::VOTES_COUNT] ?? $faker->numberBetween(0));
        $entity->setVotesPercent($params[self::VOTES_PERCENT] ?? $faker->numberBetween(0, 99) + 0.99);
        $entity->setDecisionLink($params[self::DECISION_LINK] ?? null);
        $entity->setCeasingReason($params[self::CEASING_REASON] ?? null);
        $entity->setCeasingLink($params[self::CEASING_LINK] ?? null);
        $entity->setCompetenceUsesUpdateTime($params[self::COMPETENCE_USE_UPDATE_TIME] ?? null);

        return $entity;
    }
}
<?php

namespace App\Validator;

use App\Repository\CompetenceRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DeletableCompetenceValidator extends ConstraintValidator
{
    protected $competenceRepository;

    public function __construct(CompetenceRepository $competenceRepository)
    {
        $this->competenceRepository = $competenceRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\DeletableCompetence */

        if ($this->competenceRepository->hasConnections($value)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}

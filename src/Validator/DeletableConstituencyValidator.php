<?php

namespace App\Validator;

use App\Repository\ConstituencyRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DeletableConstituencyValidator extends ConstraintValidator
{
    protected $constituencyRepository;

    public function __construct(ConstituencyRepository $constituencyRepository)
    {
        $this->constituencyRepository = $constituencyRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\DeletableConstituency */

        if ($this->constituencyRepository->hasConnections($value)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}

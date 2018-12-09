<?php

namespace App\Validator;

use App\Repository\MandateRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DeletableMandateValidator extends ConstraintValidator
{
    protected $statusRepository;

    public function __construct(MandateRepository $statusRepository)
    {
        $this->statusRepository = $statusRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\DeletableMandate */

        if ($this->statusRepository->hasConnections($value)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}

<?php

namespace App\Validator;

use App\Repository\PoliticianRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DeletablePoliticianValidator extends ConstraintValidator
{
    protected $statusRepository;

    public function __construct(PoliticianRepository $statusRepository)
    {
        $this->statusRepository = $statusRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\DeletablePolitician */

        if ($this->statusRepository->hasConnections($value)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}

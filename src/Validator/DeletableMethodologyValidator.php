<?php

namespace App\Validator;

use App\Repository\MethodologyRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DeletableMethodologyValidator extends ConstraintValidator
{
    protected $repository;

    public function __construct(MethodologyRepository $repository)
    {
        $this->repository = $repository;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\DeletableMethodology */

        if ($this->repository->hasConnections($value)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}

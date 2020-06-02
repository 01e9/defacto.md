<?php

namespace App\Validator;

use App\Repository\PromiseActionRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DeletableActionValidator extends ConstraintValidator
{
    protected $actionRepository;

    public function __construct(PromiseActionRepository $actionRepository)
    {
        $this->actionRepository = $actionRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\DeletableAction */

        if ($this->actionRepository->hasConnections($value)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}

<?php

namespace App\Validator;

use App\Repository\PromiseRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DeletablePromiseValidator extends ConstraintValidator
{
    protected $promiseRepository;

    public function __construct(PromiseRepository $promiseRepository)
    {
        $this->promiseRepository = $promiseRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\DeletablePromise */

        if ($this->promiseRepository->hasConnections($value)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}

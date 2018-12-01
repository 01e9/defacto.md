<?php

namespace App\Validator;

use App\Repository\StatusRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use App\Validator\DeletableStatus;

class DeletableStatusValidator extends ConstraintValidator
{
    protected $statusRepository;

    public function __construct(StatusRepository $statusRepository)
    {
        $this->statusRepository = $statusRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof DeletableStatus) {
            throw new UnexpectedTypeException($constraint, DeletableStatus::class);
        }

        if ($this->statusRepository->hasConnections($value)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}

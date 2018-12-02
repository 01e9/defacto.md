<?php

namespace App\Validator;

use App\Repository\ActionRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use App\Validator\DeletableAction;

class DeletableActionValidator extends ConstraintValidator
{
    protected $actionRepository;

    public function __construct(ActionRepository $actionRepository)
    {
        $this->actionRepository = $actionRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof DeletableAction) {
            throw new UnexpectedTypeException($constraint, DeletableAction::class);
        }

        if ($this->actionRepository->hasConnections($value)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}

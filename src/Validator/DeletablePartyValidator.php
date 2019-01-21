<?php

namespace App\Validator;

use App\Repository\PartyRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DeletablePartyValidator extends ConstraintValidator
{
    protected $statusRepository;

    public function __construct(PartyRepository $statusRepository)
    {
        $this->statusRepository = $statusRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\DeletableParty */

        if ($this->statusRepository->hasConnections($value)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}

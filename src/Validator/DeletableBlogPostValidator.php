<?php

namespace App\Validator;

use App\Repository\BlogPostRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DeletableBlogPostValidator extends ConstraintValidator
{
    protected $problemRepository;

    public function __construct(BlogPostRepository $problemRepository)
    {
        $this->problemRepository = $problemRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\DeletableBlogPost */

        if ($this->problemRepository->hasConnections($value)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}

<?php

namespace App\Validator;

use App\Repository\BlogPostRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DeletableBlogPostValidator extends ConstraintValidator
{
    protected $repository;

    public function __construct(BlogPostRepository $repository)
    {
        $this->repository = $repository;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\DeletableBlogPost */

        if ($this->repository->hasConnections($value)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}

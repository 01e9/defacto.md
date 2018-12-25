<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DeletableProblem extends Constraint
{
    public $message = 'not_deletable.problem';
}

<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DeletableBlogPost extends Constraint
{
    public $message = 'not_deletable.problem';
}

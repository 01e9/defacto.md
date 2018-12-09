<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DeletableMandate extends Constraint
{
    public $message = 'not_deletable.mandate';
}

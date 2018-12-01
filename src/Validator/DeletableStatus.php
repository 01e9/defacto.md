<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DeletableStatus extends Constraint
{
    public $message = 'not_deletable.status';
}

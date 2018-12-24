<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DeletableConstituency extends Constraint
{
    public $message = 'not_deletable.constituency';
}

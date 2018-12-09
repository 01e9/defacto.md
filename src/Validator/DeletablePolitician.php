<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DeletablePolitician extends Constraint
{
    public $message = 'not_deletable.politician';
}

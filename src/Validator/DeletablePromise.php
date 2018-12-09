<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DeletablePromise extends Constraint
{
    public $message = 'not_deletable.promise';
}

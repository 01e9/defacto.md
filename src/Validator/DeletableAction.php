<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DeletableAction extends Constraint
{
    public $message = 'not_deletable.action';
}

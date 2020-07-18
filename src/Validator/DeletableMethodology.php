<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DeletableMethodology extends Constraint
{
    public $message = 'not_deletable.methodology';
}

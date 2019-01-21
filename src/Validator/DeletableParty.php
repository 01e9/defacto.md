<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DeletableParty extends Constraint
{
    public $message = 'not_deletable.party';
}

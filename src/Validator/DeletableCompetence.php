<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DeletableCompetence extends Constraint
{
    public $message = 'not_deletable.competence';
}

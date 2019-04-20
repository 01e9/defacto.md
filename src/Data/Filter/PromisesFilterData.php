<?php

namespace App\Data\Filter;

use App\Entity\Election;
use App\Entity\Politician;

class PromisesFilterData
{
    /**
     * @var string
     */
    public $code;

    /**
     * @var Politician
     */
    public $politician;

    /**
     * @var Election
     */
    public $election;
}

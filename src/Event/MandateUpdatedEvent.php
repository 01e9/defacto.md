<?php

namespace App\Event;

use App\Entity\Mandate;
use Symfony\Contracts\EventDispatcher\Event;

class MandateUpdatedEvent extends Event
{
    private Mandate $mandate;

    public function __construct(Mandate $mandate)
    {
        $this->mandate = $mandate;
    }

    public function getMandate(): Mandate
    {
        return $this->mandate;
    }
}
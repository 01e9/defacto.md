<?php

namespace App\Event;

use App\Entity\PromiseAction;
use Symfony\Contracts\EventDispatcher\Event;

class PromiseActionUpdatedEvent extends Event
{
    private PromiseAction $promiseAction;

    public function __construct(PromiseAction $promiseAction)
    {
        $this->promiseAction = $promiseAction;
    }

    public function getPromiseAction(): PromiseAction
    {
        return $this->promiseAction;
    }
}
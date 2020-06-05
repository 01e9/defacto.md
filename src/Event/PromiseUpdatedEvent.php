<?php

namespace App\Event;

use App\Entity\Promise;
use Symfony\Contracts\EventDispatcher\Event;

class PromiseUpdatedEvent extends Event
{
    private Promise $promise;

    public function __construct(Promise $promise)
    {
        $this->promise = $promise;
    }

    public function getPromise(): Promise
    {
        return $this->promise;
    }
}
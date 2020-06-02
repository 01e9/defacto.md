<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PromiseUpdateRepository")
 * @ORM\Table(
 *     name="promise_updates",
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(
 *          name="promise_update_unique_action_promise",
 *          columns={"action_id", "promise_id"}
 *      )
 *     }
 * )
 *
 * @UniqueEntity(fields={"action", "promise"}, errorPath="promise")
 */
class PromiseUpdate
{
    use Traits\IdTrait;

    /**
     * @ORM\ManyToOne(targetEntity="PromiseAction", inversedBy="promiseUpdates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $action;

    /**
     * @ORM\ManyToOne(targetEntity="Promise", inversedBy="promiseUpdates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $promise;

    /**
     * @ORM\ManyToOne(targetEntity="Status")
     * @ORM\JoinColumn(nullable=true)
     */
    private $status;

    public function getAction() : ?PromiseAction
    {
        return $this->action;
    }

    public function setAction(?PromiseAction $action) : self
    {
        $this->action = $action;

        return $this;
    }

    public function getPromise() : ?Promise
    {
        return $this->promise;
    }

    public function setPromise(?Promise $promise) : self
    {
        $this->promise = $promise;

        return $this;
    }

    public function getStatus() : ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status) : self
    {
        $this->status = $status;

        return $this;
    }
}

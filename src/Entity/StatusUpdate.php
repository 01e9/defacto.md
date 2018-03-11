<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StatusUpdateRepository")
 * @ORM\Table(
 *     name="status_updates",
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(
 *          name="status_update_unique_action_promise",
 *          columns={"action_id", "promise_id"}
 *      )
 *     }
 * )
 *
 * @UniqueEntity(fields={"action", "promise"}, errorPath="promise")
 */
class StatusUpdate
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=36)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Action", inversedBy="statusUpdates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $action;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Promise")
     * @ORM\JoinColumn(nullable=false)
     */
    private $promise;

    /**
     * @ORM\ManyToOne(targetEntity="Status")
     * @ORM\JoinColumn(nullable=false)
     */
    private $status;

    public function getId() : ?string
    {
        return $this->id;
    }

    public function getAction() : ?Action
    {
        return $this->action;
    }

    public function setAction(?Action $action) : StatusUpdate
    {
        $this->action = $action;

        return $this;
    }

    public function getPromise() : ?Promise
    {
        return $this->promise;
    }

    public function setPromise(?Promise $promise) : StatusUpdate
    {
        $this->promise = $promise;

        return $this;
    }

    public function getStatus() : ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status) : StatusUpdate
    {
        $this->status = $status;

        return $this;
    }
}

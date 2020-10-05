<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PromiseActionRepository")
 * @ORM\Table(
 *     name="promise_actions",
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(name="promise_action_unique_slug", columns={"slug"})
 *     }
 * )
 *
 * @UniqueEntity(fields={"slug"})
 */
class PromiseAction
{
    use Traits\IdTrait;
    use Traits\NameTrait;
    use Traits\SlugTrait;
    use Traits\DescriptionTrait;

    /**
     * @ORM\Column(name="occurred_time", type="date")
     */
    private $occurredTime;

    /**
     * @ORM\ManyToOne(targetEntity="Mandate")
     * @ORM\JoinColumn(nullable=false)
     */
    private $mandate;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $published;

    /**
     * @ORM\OneToMany(targetEntity="PromiseUpdate", mappedBy="action", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\Valid()
     */
    private $promiseUpdates;

    /**
     * @ORM\ManyToMany(targetEntity="Power", cascade={"persist"})
     */
    private $usedPowers;

    /**
     * @ORM\OneToMany(targetEntity="PromiseActionSource", mappedBy="action", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\Valid()
     */
    private $sources;

    public function __construct()
    {
        $this->promiseUpdates = new ArrayCollection();
        $this->usedPowers = new ArrayCollection();
        $this->sources = new ArrayCollection();
    }

    public function getOccurredTime() : ?\DateTime
    {
        return $this->occurredTime;
    }

    public function setOccurredTime(?\DateTime $date) : PromiseAction
    {
        $this->occurredTime = $date;

        return $this;
    }

    public function getMandate() : ?Mandate
    {
        return $this->mandate;
    }

    public function setMandate(?Mandate $mandate) : PromiseAction
    {
        $this->mandate = $mandate;

        return $this;
    }

    public function getPublished() : ?bool
    {
        return $this->published;
    }

    public function setPublished(?bool $published) : PromiseAction
    {
        $this->published = $published;

        return $this;
    }

    public function getPromiseUpdates()
    {
        return $this->promiseUpdates;
    }

    public function setPromiseUpdates($promiseUpdates) : PromiseAction
    {
        $this->promiseUpdates = $promiseUpdates;

        return $this;
    }

    public function getUsedPowers()
    {
        return $this->usedPowers;
    }

    public function setUsedPowers($usedPowers) : PromiseAction
    {
        $this->usedPowers = $usedPowers;

        return $this;
    }

    public function getSources()
    {
        return $this->sources;
    }
}

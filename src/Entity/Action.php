<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActionRepository")
 * @ORM\Table(
 *     name="actions",
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(name="action_unique_slug", columns={"slug"})
 *     }
 * )
 */
class Action
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=36)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\Column(name="occurred_time", type="date")
     */
    private $occurredTime;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups({"searchable"})
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=50)
     */
    private $name;

    /**
     * @ORM\Column(name="slug", type="string", length=50)
     * @Groups({"searchable"})
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=50)
     * @Assert\Regex(
     *     pattern="/^\p{L}+(\-\p{L}+)*$/u",
     *     message="invalid.slug"
     * )
     */
    private $slug;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Assert\Length(min=3, max=10000)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Mandate")
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
     * @ORM\ManyToMany(targetEntity="App\Entity\Power", cascade={"persist"})
     */
    private $usedPowers;

    /**
     * @ORM\OneToMany(targetEntity="ActionSource", mappedBy="action", cascade={"persist", "remove"})
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

    public function getId() : ?string
    {
        return $this->id;
    }

    public function getOccurredTime() : ?\DateTime
    {
        return $this->occurredTime;
    }

    public function setOccurredTime(?\DateTime $date) : Action
    {
        $this->occurredTime = $date;

        return $this;
    }

    public function getName() : ?string
    {
        return $this->name;
    }

    public function setName(?string $name) : Action
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug() : ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug) : Action
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription() : ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description) : Action
    {
        $this->description = $description;

        return $this;
    }

    public function getMandate() : ?Mandate
    {
        return $this->mandate;
    }

    public function setMandate(?Mandate $mandate) : Action
    {
        $this->mandate = $mandate;

        return $this;
    }

    public function getPublished() : ?bool
    {
        return $this->published;
    }

    public function setPublished(?bool $published) : Action
    {
        $this->published = $published;

        return $this;
    }

    public function getPromiseUpdates()
    {
        return $this->promiseUpdates;
    }

    public function setPromiseUpdates($promiseUpdates) : Action
    {
        $this->promiseUpdates = $promiseUpdates;

        return $this;
    }

    public function getUsedPowers()
    {
        return $this->usedPowers;
    }

    public function setUsedPowers($usedPowers) : Action
    {
        $this->usedPowers = $usedPowers;

        return $this;
    }

    public function getSources()
    {
        return $this->sources;
    }
}

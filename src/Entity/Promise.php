<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PromiseRepository")
 * @ORM\Table(
 *     name="promises",
 *     indexes={
 *      @ORM\Index(name="promise_index_made_time", columns={"made_time"})
 *     },
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(name="promise_unique_slug", columns={"slug"}),
 *      @ORM\UniqueConstraint(name="promise_unique_code", columns={"code"})
 *     }
 * )
 *
 * @UniqueEntity(fields={"slug"})
 * @UniqueEntity(fields={"code"})
 */
class Promise
{
    use Traits\IdTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Election")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\NotBlank()
     */
    private $election;

    /**
     * @ORM\ManyToOne(targetEntity="Politician")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\NotBlank()
     */
    private $politician;

    /**
     * @ORM\ManyToOne(targetEntity="Status")
     * @ORM\JoinColumn(nullable=true)
     */
    private $status;

    /**
     * @ORM\ManyToMany(targetEntity="PromiseCategory")
     */
    private $categories;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="date")
     */
    private $madeTime;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     *
     * @Assert\Length(max=10)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=120)
     * @Groups({"searchable"})
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=120)
     */
    private $name;

    /**
     * @ORM\Column(name="slug", type="string", length=120)
     * @Groups({"searchable"})
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=120)
     * @Assert\Regex(pattern="/^[a-z\d]+(\-[a-z\d]+)*$/", message="invalid.slug")
     */
    private $slug;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Assert\Length(min=3, max=10000)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $published;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $hasPrerogatives;

    /**
     * @ORM\OneToMany(targetEntity="PromiseUpdate", mappedBy="promise")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\Valid()
     */
    private $promiseUpdates;

    /**
     * @ORM\OneToMany(targetEntity="PromiseSource", mappedBy="promise", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\Valid()
     */
    private $sources;

    public function __construct()
    {
        $this->published = false;
        $this->hasPrerogatives = false;
        $this->categories = new ArrayCollection();
        $this->promiseUpdates = new ArrayCollection();
        $this->sources = new ArrayCollection();
    }

    public function getElection() : ?Election
    {
        return $this->election;
    }

    public function setElection(?Election $election) : self
    {
        $this->election = $election;

        return $this;
    }

    public function getPolitician() : ?Politician
    {
        return $this->politician;
    }

    public function setPolitician(?Politician $politician) : self
    {
        $this->politician = $politician;

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

    public function getPublished() : ?bool
    {
        return $this->published;
    }

    public function setPublished(?bool $published) : self
    {
        $this->published = $published;

        return $this;
    }

    public function getHasPrerogatives() : ?bool
    {
        return $this->hasPrerogatives;
    }

    public function setHasPrerogatives(?bool $hasPrerogatives) : self
    {
        $this->hasPrerogatives = $hasPrerogatives;

        return $this;
    }

    public function getName() : ?string
    {
        return $this->name;
    }

    public function setName(?string $name) : self
    {
        $this->name = $name;

        return $this;
    }

    public function getCode() : ?string
    {
        return $this->code;
    }

    public function setCode(?string $code) : self
    {
        $this->code = $code;

        return $this;
    }

    public function getSlug() : ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug) : self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription() : ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description)
    {
        $this->description = $description;

        return $this;
    }

    public function getMadeTime() : ?\DateTime
    {
        return $this->madeTime;
    }

    public function setMadeTime(?\DateTime $date) : self
    {
        $this->madeTime = $date;

        return $this;
    }

    public function getCategories()
    {
        return $this->categories;
    }

    public function setCategories($categories) : self
    {
        $this->categories = $categories;

        return $this;
    }

    public function getPromiseUpdates()
    {
        return $this->promiseUpdates;
    }

    public function getPublishedPromiseUpdatesSortedByActionDate()
    {
        $promiseUpdates = $this->promiseUpdates->toArray();

        if ($promiseUpdates) {
            \uasort($promiseUpdates, function(PromiseUpdate $a, PromiseUpdate $b) : int {
                if ($a->getAction()->getOccurredTime() == $b->getAction()->getOccurredTime()) {
                    return 0;
                }
                return ($a->getAction()->getOccurredTime() < $b->getAction()->getOccurredTime()) ? -1 : 1;
            });
        }

        return $promiseUpdates;
    }

    public function getSources()
    {
        return $this->sources;
    }
}

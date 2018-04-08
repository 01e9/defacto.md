<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PromiseRepository")
 * @ORM\Table(
 *     name="promises",
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(name="promise_unique_slug_mandate", columns={"slug", "mandate_id"})
 *     }
 * )
 */
class Promise
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=36)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Mandate")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\NotBlank()
     */
    private $mandate;

    /**
     * @ORM\ManyToOne(targetEntity="Status", fetch="EAGER")
     * @ORM\JoinColumn(nullable=true)
     */
    private $status;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Category")
     */
    private $categories;

    /**
     * @ORM\Column(type="date")
     *
     * @Assert\Date()
     */
    private $madeTime;

    /**
     * @ORM\Column(type="string")
     * @Groups({"searchable"})
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=3)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $published;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\Length(min=3)
     */
    private $sourceName;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\Length(min=3)
     * @Assert\Url(checkDNS=false)
     */
    private $sourceLink;

    /**
     * @ORM\OneToMany(targetEntity="PromiseUpdate", mappedBy="promise")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\Valid()
     */
    private $promiseUpdates;

    public function __construct()
    {
        $this->published = false;
        $this->categories = new ArrayCollection();
        $this->promiseUpdates = new ArrayCollection();
    }

    public function getId() : ?string
    {
        return $this->id;
    }

    public function getSlug() : ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug) : Promise
    {
        $this->slug = $slug;

        return $this;
    }

    public function getMandate() : ?Mandate
    {
        return $this->mandate;
    }

    public function setMandate(?Mandate $mandate) : Promise
    {
        $this->mandate = $mandate;

        return $this;
    }

    public function getStatus() : ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status) : Promise
    {
        $this->status = $status;

        return $this;
    }

    public function getPublished() : ?bool
    {
        return $this->published;
    }

    public function setPublished(?bool $published) : Promise
    {
        $this->published = $published;

        return $this;
    }

    public function getName() : ?string
    {
        return $this->name;
    }

    public function setName(?string $name) : Promise
    {
        $this->name = $name;

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

    public function setMadeTime(?\DateTime $date) : Promise
    {
        $this->madeTime = $date;

        return $this;
    }

    public function getCategories()
    {
        return $this->categories;
    }

    public function setCategories($categories) : Promise
    {
        $this->categories = $categories;

        return $this;
    }

    public function getPromiseUpdates()
    {
        return $this->promiseUpdates;
    }

    public function getSourceName() : ?string
    {
        return $this->sourceName;
    }

    public function setSourceName(?string $sourceName) : Promise
    {
        $this->sourceName = $sourceName;

        return $this;
    }

    public function getSourceLink() : ?string
    {
        return $this->sourceLink;
    }

    public function setSourceLink(?string $sourceLink) : Promise
    {
        $this->sourceLink = $sourceLink;

        return $this;
    }

    public function getPublishedPromiseUpdatesSortedByActionDate()
    {
        $promiseUpdates = $this->promiseUpdates->toArray();

        if ($promiseUpdates) {
            \uasort($promiseUpdates, function(PromiseUpdate $a, PromiseUpdate $b) : int {
                if ($a->getAction()->getOccurredTime() == $b->getAction()->getOccurredTime()) {
                    return 0;
                }
                return ($a->getAction()->getOccurredTime() > $b->getAction()->getOccurredTime()) ? -1 : 1;
            });
        }

        return $promiseUpdates;
    }
}

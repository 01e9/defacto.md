<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

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
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Mandate")
     * @ORM\JoinColumn(nullable=false)
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
     */
    private $madeTime;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $published;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->published = false;
    }

    public function getId() : ?string
    {
        return $this->id;
    }

    public function getSlug() : ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug) : Promise
    {
        $this->slug = $slug;

        return $this;
    }

    public function getMandate() : ?Mandate
    {
        return $this->mandate;
    }

    public function setMandate(Mandate $mandate) : Promise
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

    public function setPublished(bool $published) : Promise
    {
        $this->published = $published;

        return $this;
    }

    public function getTitle() : ?string
    {
        return $this->title;
    }

    public function setTitle(string $title) : Promise
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription() : ?string
    {
        return $this->description;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }

    public function getMadeTime() : ?\DateTime
    {
        return $this->madeTime;
    }

    public function setMadeTime(\DateTime $date) : Promise
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
}

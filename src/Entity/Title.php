<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TitleRepository")
 * @ORM\Table(
 *     name="titles",
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(name="title_unique_slug", columns={"slug"})
 *     }
 * )
 *
 * @UniqueEntity(fields={"slug"})
 */
class Title
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=36)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=50)
     * @Assert\Regex(pattern="/^[\p{L}\d]+(\-[\p{L}\d]+)*$/u", message="invalid.slug")
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=50)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=50)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=50)
     */
    private $theName;

    /**
     * @ORM\ManyToMany(targetEntity="Power", cascade={"persist"})
     * @ORM\OrderBy({"name"="ASC"})
     */
    private $powers;

    public function __construct()
    {
        $this->powers = new ArrayCollection();
    }

    public function getId() : ?string
    {
        return $this->id;
    }

    public function getSlug() : ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug) : Title
    {
        $this->slug = $slug;

        return $this;
    }

    public function getName() : ?string
    {
        return $this->name;
    }

    public function setName(?string $name) : Title
    {
        $this->name = $name;

        return $this;
    }

    public function getTheName() : ?string
    {
        return $this->theName;
    }

    public function setTheName(?string $theName) : Title
    {
        $this->theName = $theName;

        return $this;
    }

    public function getPowers()
    {
        return $this->powers;
    }

    public function setPowers($powers) : Title
    {
        $this->powers = $powers;

        return $this;
    }
}

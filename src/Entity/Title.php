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
    use Traits\IdTrait;

    /**
     * @ORM\Column(type="string", length=50)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=50)
     * @Assert\Regex(pattern="/^[a-z\d]+(\-[a-z\d]+)*$/", message="invalid.slug")
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
    private $nameFemale;

    /**
     * @ORM\Column(type="string", length=50)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=50)
     */
    private $theName;

    /**
     * @ORM\Column(type="string", length=50)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=50)
     */
    private $theNameFemale;

    /**
     * @ORM\ManyToMany(targetEntity="Power", cascade={"persist"})
     * @ORM\OrderBy({"name"="ASC"})
     */
    private $powers;

    public function __construct()
    {
        $this->powers = new ArrayCollection();
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

    public function getName() : ?string
    {
        return $this->name;
    }

    public function setName(?string $name) : self
    {
        $this->name = $name;

        return $this;
    }

    public function getNameFemale() : ?string
    {
        return $this->nameFemale;
    }

    public function setNameFemale(?string $nameFemale) : self
    {
        $this->nameFemale = $nameFemale;

        return $this;
    }

    public function getTheName() : ?string
    {
        return $this->theName;
    }

    public function setTheName(?string $theName) : self
    {
        $this->theName = $theName;

        return $this;
    }

    public function getTheNameFemale() : ?string
    {
        return $this->theNameFemale;
    }

    public function setTheNameFemale(?string $theNameFemale) : self
    {
        $this->theNameFemale = $theNameFemale;

        return $this;
    }

    public function getPowers()
    {
        return $this->powers;
    }

    public function setPowers($powers) : self
    {
        $this->powers = $powers;

        return $this;
    }
}

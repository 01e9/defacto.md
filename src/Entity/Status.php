<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StatusRepository")
 * @ORM\Table(
 *     name="statuses",
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(name="status_unique_slug", columns={"slug"}),
 *      @ORM\UniqueConstraint(name="status_unique_effect", columns={"effect"})
 *     }
 * )
 *
 * @UniqueEntity(fields={"slug"})
 * @UniqueEntity(fields={"effect"})
 */
class Status
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
    private $namePlural;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Length(min=3, max=255)
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $effect;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     *
     * @Assert\Regex(pattern="/^[a-z\-]{3,20}$/", message="invalid.color")
     */
    private $color;

    public function getId() : ?string
    {
        return $this->id;
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

    public function getNamePlural() : ?string
    {
        return $this->namePlural;
    }

    public function setNamePlural(?string $namePlural) : self
    {
        $this->namePlural = $namePlural;

        return $this;
    }

    public function getDescription() : ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description) : self
    {
        $this->description = $description;

        return $this;
    }

    public function getEffect() : ?int
    {
        return $this->effect;
    }

    public function setEffect(?int $effect) : self
    {
        $this->effect = $effect;

        return $this;
    }

    public function getColor() : ?string
    {
        return $this->color;
    }

    public function setColor(?string $color) : self
    {
        $this->color = $color;

        return $this;
    }
}

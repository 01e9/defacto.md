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
     * @Assert\Length(min=3, max=30)
     * @Assert\Regex(
     *     pattern="/^\p{L}+(\-\p{L}+)*$/u",
     *     message="invalid.slug"
     * )
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
     * @ORM\Column(type="integer")
     */
    private $effect;

    /**
     * @ORM\Column(type="string", length=7, nullable=true)
     *
     * @Assert\Regex(
     *     pattern="/^#([a-f0-9]{3}){2}$/i",
     *     message="invalid.color"
     * )
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

    public function setSlug(?string $slug) : Status
    {
        $this->slug = $slug;

        return $this;
    }

    public function getName() : ?string
    {
        return $this->name;
    }

    public function setName(?string $name) : Status
    {
        $this->name = $name;

        return $this;
    }

    public function getNamePlural() : ?string
    {
        return $this->namePlural;
    }

    public function setNamePlural(?string $namePlural) : Status
    {
        $this->namePlural = $namePlural;

        return $this;
    }

    public function getEffect() : ?int
    {
        return $this->effect;
    }

    public function setEffect(?int $effect) : Status
    {
        $this->effect = $effect;

        return $this;
    }

    public function getColor() : ?string
    {
        return $this->color;
    }

    public function setColor(?string $color) : Status
    {
        $this->color = $color;

        return $this;
    }
}

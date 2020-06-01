<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PartyRepository")
 * @ORM\Table(
 *     name="parties",
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(name="party_unique_slug", columns={"slug"})
 *     }
 * )
 *
 * @UniqueEntity(fields={"slug"})
 */
class Party
{
    use Traits\IdTrait;

    /**
     * @ORM\Column(type="string", length=120)
     * @Groups({"searchable"})
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=120)
     * @Assert\Regex(pattern="/^[a-z\d]+(\-[a-z\d]+)*$/", message="invalid.slug")
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=120)
     * @Groups({"searchable"})
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=120)
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\File(mimeTypes={"image/jpeg", "image/png", "image/gif"})
     */
    private $logo;

    public function getSlug() : ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug) : self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return File
     */
    public function getLogo()
    {
        return $this->logo;
    }

    public function setLogo($logo) : self
    {
        $this->logo = $logo;

        return $this;
    }
}

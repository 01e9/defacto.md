<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompetenceCategoryRepository")
 * @ORM\Table(
 *     name="competence_categories",
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(name="competence_category_unique_slug", columns={"slug"})
 *     }
 * )
 *
 * @UniqueEntity(fields={"slug"})
 */
class CompetenceCategory
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
     * @var CompetenceCategory|null
     *
     * @ORM\ManyToOne(targetEntity="CompetenceCategory")
     * @ORM\JoinColumn(nullable=true)
     */
    private $parent;

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

    public function getParent(): ?CompetenceCategory
    {
        return $this->parent;
    }

    public function setParent(?CompetenceCategory $parent): self
    {
        $this->parent = $parent;

        return $this;
    }
}

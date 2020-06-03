<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompetenceRepository")
 * @ORM\Table(
 *     name="competences",
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(name="competence_unique_title_slug", columns={"title_id", "slug"}),
 *      @ORM\UniqueConstraint(name="competence_unique_title_code", columns={"title_id", "code"})
 *     }
 * )
 *
 * @UniqueEntity(fields={"title", "slug"}, errorPath="slug")
 * @UniqueEntity(fields={"title", "code"}, errorPath="code")
 */
class Competence
{
    use Traits\IdTrait;
    use Traits\SlugTrait;
    use Traits\NameTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Title")
     * @ORM\JoinColumn(nullable=false)
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="CompetenceCategory", fetch="EAGER")
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=10, nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=10)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=255)
     */
    private $name;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=1, nullable=false)
     *
     * @Assert\GreaterThan(0)
     */
    private $points;

    public function __construct()
    {
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

    public function getPoints(): ?float
    {
        return $this->points;
    }

    public function setPoints(?float $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function getTitle(): ?Title
    {
        return $this->title;
    }

    public function setTitle(?Title $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCategory(): ?CompetenceCategory
    {
        return $this->category;
    }

    public function setCategory(?CompetenceCategory $category): self
    {
        $this->category = $category;

        return $this;
    }
}

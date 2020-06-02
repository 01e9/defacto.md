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
 *      @ORM\UniqueConstraint(name="competence_unique_slug", columns={"slug"}),
 *      @ORM\UniqueConstraint(name="competence_unique_code", columns={"code"})
 *     }
 * )
 *
 * @UniqueEntity(fields={"slug"})
 * @UniqueEntity(fields={"code"})
 */
class Competence
{
    use Traits\IdTrait;
    use Traits\SlugTrait;
    use Traits\DescriptionTrait;

    /**
     * @ORM\ManyToOne(targetEntity="CompetenceCategory")
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     *
     * @Assert\Length(max=10)
     */
    private $code;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=1, nullable=false)
     *
     * @Assert\GreaterThan(0)
     */
    private $points;

    /**
     * @ORM\Column(type="text", nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=10000)
     */
    private $description;

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
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompetenceUseRepository")
 * @ORM\Table(
 *     name="competence_uses"
 * )
 */
class CompetenceUse
{
    use Traits\IdTrait;
    use Traits\DescriptionTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Mandate", inversedBy="competenceUses")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $mandate;

    /**
     * @ORM\ManyToOne(targetEntity="Competence")
     * @ORM\JoinColumn(nullable=false)
     */
    private $competence;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="date")
     */
    private $useDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Length(max=255)
     * @Assert\Url()
     */
    private $sourceLink;

    public function getMandate(): ?Mandate
    {
        return $this->mandate;
    }

    public function setMandate(?Mandate $mandate): self
    {
        $this->mandate = $mandate;

        return $this;
    }

    public function getCompetence(): ?Competence
    {
        return $this->competence;
    }

    public function setCompetence(?Competence $competence): self
    {
        $this->competence = $competence;

        return $this;
    }

    public function getUseTime(): \DateTimeInterface
    {
        return $this->useTime;
    }

    public function setUseTime(\DateTimeInterface $useTime): self
    {
        $this->useTime = $useTime;

        return $this;
    }

    public function getSourceLink(): ?string
    {
        return $this->sourceLink;
    }

    public function setSourceLink(?string $sourceLink): self
    {
        $this->sourceLink = $sourceLink;

        return $this;
    }
}

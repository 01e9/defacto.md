<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompetenceUseRepository")
 * @ORM\Table(
 *     name="competence_uses",
 *     indexes={
 *      @ORM\Index(name="competence_use_mandate_use_data", columns={"mandate_id", "use_date"})
 *     }
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

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $isMultiplied = false;

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

    public function getUseDate(): ?\DateTimeInterface
    {
        return $this->useDate;
    }

    public function setUseDate(?\DateTimeInterface $useDate): self
    {
        $this->useDate = $useDate;

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

    public function isMultiplied(): bool
    {
        return $this->isMultiplied;
    }

    public function setIsMultiplied(bool $isMultiplied): self
    {
        $this->isMultiplied = $isMultiplied;

        return $this;
    }

}

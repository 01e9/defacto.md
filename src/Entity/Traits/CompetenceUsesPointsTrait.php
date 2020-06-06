<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait CompetenceUsesPointsTrait
{
    /**
     * @ORM\Column(type="decimal", precision=5, scale=1, options={"default": 0}, nullable=false)
     *
     * @Assert\GreaterThanOrEqual(0)
     */
    private $competenceUsesPoints = 0;

    public function getCompetenceUsesPoints(): ?float
    {
        return $this->competenceUsesPoints;
    }

    public function setCompetenceUsesPoints(?float $competenceUsesPoints): self
    {
        $this->competenceUsesPoints = $competenceUsesPoints;

        return $this;
    }
}

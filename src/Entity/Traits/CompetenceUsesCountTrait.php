<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait CompetenceUsesCountTrait
{
    /**
     * @ORM\Column(type="integer", options={"default": 0})
     *
     * @Assert\GreaterThanOrEqual(0)
     */
    private $competenceUsesCount;

    public function getCompetenceUsesCount(): ?int
    {
        return $this->competenceUsesCount;
    }

    public function setCompetenceUsesCount(?int $competenceUsesCount): self
    {
        $this->competenceUsesCount = $competenceUsesCount;

        return $this;
    }
}

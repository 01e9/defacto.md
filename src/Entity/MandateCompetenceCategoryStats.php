<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MandateCompetenceCategoryStatsRepository")
 * @ORM\Table(
 *     name="mandate_competence_category_stats",
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(
 *          name="mandate_competence_category_stats_unique_category",
 *          columns={"mandate_id", "competence_category_id"}
 *      )
 *     }
 * )
 *
 * @UniqueEntity(fields={"mandate", "competenceCategory"}, errorPath="competenceCategory")
 */
class MandateCompetenceCategoryStats
{
    use Traits\IdTrait;
    use Traits\CompetenceUsesCountTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Mandate", inversedBy="competenceCategoryStats")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $mandate;

    /**
     * @ORM\ManyToOne(targetEntity="CompetenceCategory", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $competenceCategory;

    public function getMandate(): ?Mandate
    {
        return $this->mandate;
    }

    public function setMandate(?Mandate $mandate): self
    {
        $this->mandate = $mandate;

        return $this;
    }

    public function getCompetenceCategory(): ?CompetenceCategory
    {
        return $this->competenceCategory;
    }

    public function setCompetenceCategory(?CompetenceCategory $competenceCategory): self
    {
        $this->competenceCategory = $competenceCategory;

        return $this;
    }
}

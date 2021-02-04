<?php

namespace App\Vo;

use App\Entity\MandateCompetenceCategoryStats;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class MandateCompetenceUseStatsVo
{
    public int $useCount = 0;

    public float $usePoints = 0;

    /** @var Collection|MandateCompetenceCategoryStats[] */
    public Collection $categoryStats;

    public function __construct()
    {
        $this->categoryStats = new ArrayCollection();
    }
}
<?php

namespace App\Repository\Vo;

use App\Entity\Mandate;

class MandateStatisticsVo
{
    public ?Mandate $mandate;
    public ?array $promiseStatistics;
}
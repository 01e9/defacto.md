<?php

namespace App\Repository\Vo;

use App\Entity\Election;
use App\Entity\Mandate;

class ElectionDataVo
{
    public ?Election $election;

    /** @var Mandate[] */
    public ?array $mandates;

    /** @var ConstituencyElectionVo[] */
    public ?array $constituencies;
}
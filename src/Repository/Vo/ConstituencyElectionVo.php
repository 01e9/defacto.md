<?php

namespace App\Repository\Vo;

use App\Entity\Constituency;
use App\Entity\Election;
use App\Entity\Mandate;

class ConstituencyElectionVo
{
    public ?Constituency $constituency;
    public ?Election $election;
    public ?Mandate $mandate;
}
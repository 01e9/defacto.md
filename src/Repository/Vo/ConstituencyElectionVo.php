<?php

namespace App\Repository\Vo;

use App\Entity\Constituency;
use App\Entity\Election;

class ConstituencyElectionVo
{
    public ?Constituency $constituency;
    public ?Election $election;
}
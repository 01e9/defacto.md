<?php

namespace App\Tests\traits;

trait UtilsTrait
{
    protected static function randomNumber() : string
    {
        return (time() % 1000) . mt_rand(100, 999);
    }
}

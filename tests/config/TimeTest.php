<?php

namespace App\Tests\config;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TimeTest extends KernelTestCase
{
    public function testTimezone()
    {
        self::bootKernel();

        $this->assertEquals('Europe/Chisinau', (new \DateTime())->getTimezone()->getName());
    }
}
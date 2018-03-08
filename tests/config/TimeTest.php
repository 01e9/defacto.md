<?php

namespace App\Tests\config;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TimeTest extends WebTestCase
{
    public function testTimezone()
    {
        self::bootKernel();

        $this->assertEquals('Europe/Chisinau', (new \DateTime())->getTimezone()->getName());
    }
}
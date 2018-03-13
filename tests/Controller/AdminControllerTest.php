<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;

class AdminControllerTest extends WebTestCase
{
    use TestCaseTrait;

    public function testIndexAction()
    {
        $client = static::createClient();
        $client->insulate();
        $this->assertTrue(self::onlyAdminCanAccess('/admin', $client));
    }
}
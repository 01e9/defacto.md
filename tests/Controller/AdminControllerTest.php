<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;

class AdminControllerTest extends WebTestCase
{
    use TestCaseTrait;

    public function testIndexAction()
    {
        $this->assertOnlyAdminCanAccess('/admin');
    }
}
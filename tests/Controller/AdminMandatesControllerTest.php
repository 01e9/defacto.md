<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;
use Doctrine\Common\Persistence\ObjectManager;

class AdminMandatesControllerTest extends WebTestCase
{
    use TestCaseTrait;

    public function testAddAction()
    {
        $client = static::createClient();
        $this->assertTrue(self::onlyAdminCanAccess('/admin/mandates/add', $client));
    }

    public function testEditAction()
    {
        $client = static::createClient();

        /** @var ObjectManager $manager */
        $manager = $client->getContainer()->get('doctrine')->getManager();
        $mandate = $manager->getRepository('App:Mandate')->findOneBy([]);

        $this->assertTrue(self::onlyAdminCanAccess('/admin/mandates/'. $mandate->getId(), $client));
    }
}
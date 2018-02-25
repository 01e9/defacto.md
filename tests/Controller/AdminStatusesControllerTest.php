<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;
use Doctrine\Common\Persistence\ObjectManager;

class AdminStatusesControllerTest extends WebTestCase
{
    use TestCaseTrait;

    public function testAddAction()
    {
        $client = static::createClient();
        $this->assertTrue(self::onlyAdminCanAccess('/admin/statuses/add', $client));
    }

    public function testEditAction()
    {
        $client = static::createClient();

        /** @var ObjectManager $manager */
        $manager = $client->getContainer()->get('doctrine')->getManager();
        $status = $manager->getRepository('App:Status')->findOneBy([]);

        $this->assertTrue(self::onlyAdminCanAccess('/admin/statuses/'. $status->getId(), $client));
    }
}
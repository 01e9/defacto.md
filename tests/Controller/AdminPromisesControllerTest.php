<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;
use Doctrine\Common\Persistence\ObjectManager;

class AdminPromisesControllerTest extends WebTestCase
{
    use TestCaseTrait;

    public function testIndexAction()
    {
        $client = static::createClient();
        $this->assertTrue(self::onlyAdminCanAccess('/admin/promises', $client));
    }

    public function testAddAction()
    {
        $client = static::createClient();
        $this->assertTrue(self::onlyAdminCanAccess('/admin/promises/add', $client));
    }

    public function testEditAction()
    {
        $client = static::createClient();

        /** @var ObjectManager $manager */
        $manager = $client->getContainer()->get('doctrine')->getManager();
        $promise = $manager->getRepository('App:Promise')->findOneBy([]);

        $this->assertTrue(self::onlyAdminCanAccess('/admin/promises/'. $promise->getId(), $client));
    }
}
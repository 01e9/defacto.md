<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;
use Doctrine\Common\Persistence\ObjectManager;

class AdminPoliticiansControllerTest extends WebTestCase
{
    use TestCaseTrait;

    public function testIndexAction()
    {
        $client = static::createClient();
        $this->assertTrue(self::onlyAdminCanAccess('/admin/politicians', $client));
    }

    public function testAddAction()
    {
        $client = static::createClient();
        $this->assertTrue(self::onlyAdminCanAccess('/admin/politicians/add', $client));
    }

    public function testEditAction()
    {
        $client = static::createClient();

        /** @var ObjectManager $manager */
        $manager = $client->getContainer()->get('doctrine')->getManager();
        $politician = $manager->getRepository('App:Politician')->findOneBy([]);

        $this->assertTrue(self::onlyAdminCanAccess('/admin/politicians/'. $politician->getId(), $client));
    }
}
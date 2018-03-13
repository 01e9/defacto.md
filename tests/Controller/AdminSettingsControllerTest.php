<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;
use Doctrine\Common\Persistence\ObjectManager;

class AdminSettingsControllerTest extends WebTestCase
{
    use TestCaseTrait;

    public function testIndexAction()
    {
        $client = static::createClient();
        $client->insulate();
        $this->assertTrue(self::onlyAdminCanAccess('/admin/settings', $client));
    }

    public function testEditAction()
    {
        $client = static::createClient();

        /** @var ObjectManager $manager */
        $manager = $client->getContainer()->get('doctrine')->getManager();
        $setting = $manager->getRepository('App:Setting')->findOneBy([]);

        $this->assertTrue(self::onlyAdminCanAccess('/admin/settings/'. $setting->getId(), $client));
    }
}
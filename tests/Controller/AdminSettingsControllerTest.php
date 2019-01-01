<?php

namespace App\Tests\Controller;

use App\Repository\SettingRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;
use Doctrine\Common\Persistence\ObjectManager;

class AdminSettingsControllerTest extends WebTestCase
{
    use TestCaseTrait;

    public function testIndexActionAccess()
    {
        $client = static::createClient();
        $client->insulate();
        $this->assertTrue(self::onlyAdminCanAccess('/admin/settings', $client));
    }

    public function testEditActionAccess()
    {
        $client = static::createClient();

        /** @var ObjectManager $manager */
        $manager = $client->getContainer()->get('doctrine')->getManager();
        $setting = $manager->getRepository('App:Setting')->findOneBy([]);

        $this->assertTrue(self::onlyAdminCanAccess('/admin/settings/'. $setting->getId(), $client));
    }

    public function testEditActionSubmit()
    {
        $client = static::createClient();
        $client->insulate();
        $client->followRedirects(false);
        self::logInClientAsRole($client, 'ROLE_ADMIN');

        $em = $client->getContainer()->get('doctrine.orm.default_entity_manager');
        $router = $client->getContainer()->get('router');

        $this->createElection($em);

        foreach (SettingRepository::getWhiteList() as $settingId => $setting) {
            foreach (self::getLangs() as $lang) {
                (function () use (&$settingId, &$setting, &$lang, &$client, &$em, &$router) {
                    $form = $client
                        ->request('GET', '/'. $lang .'/admin/settings/'. $settingId)
                        ->filter('form')->form();
                    $client->submit($form, ['setting[value]' => '']);
                    $response = $client->getResponse();

                    if (is_null($setting['default'])) {
                        $this->assertEquals(200, $response->getStatusCode());
                        $this->assertContains('is-invalid', $response->getContent());
                    } else {
                        $this->assertEquals(302, $response->getStatusCode());

                        $route = $router->match($response->getTargetUrl());
                        $this->assertEquals('admin_settings', $route['_route']);
                        $this->assertEquals($lang, $route['_locale']);
                    }
                })();

                (function () use (&$settingId, &$setting, &$lang, &$client, &$em, &$router) {
                    $formData = ['setting[value]' => ''];
                    switch ($setting['type']) {
                        case 'App:InstitutionTitle':
                        case 'App:Election':
                            $formData['setting[value]'] = $em->getRepository($setting['type'])->findOneBy([])->getId();
                            break;
                        default:
                            $this->assertTrue(false, "Unknown setting type ". $setting['type']);
                    }

                    $form = $client
                        ->request('GET', '/'. $lang .'/admin/settings/'. $settingId)
                        ->filter('form')->form();
                    $client->submit($form, $formData);
                    $response = $client->getResponse();

                    $this->assertEquals(302, $response->getStatusCode());

                    $route = $router->match($response->getTargetUrl());
                    $this->assertEquals('admin_settings', $route['_route']);
                    $this->assertEquals($lang, $route['_locale']);

                    $this->assertNotNull($em->getRepository('App:Setting')->get($settingId));
                })();
            }
        }

        $em->close();
        $em = null;
        static::$kernel->shutdown();
    }
}
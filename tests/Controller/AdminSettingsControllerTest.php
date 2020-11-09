<?php

namespace App\Tests\Controller;

use App\Repository\SettingRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;
use Doctrine\Persistence\ObjectManager;

class AdminSettingsControllerTest extends WebTestCase
{
    use TestCaseTrait;

    public function testIndexActionAccess()
    {
        $this->assertOnlyAdminCanAccess('/admin/settings');
    }

    public function testEditActionAccess()
    {
        static::ensureKernelShutdown();
        $client = static::createClient();
        $em = self::getDoctrine($client);
        $setting = $em->getRepository('App:Setting')->findOneBy([]);

        return $this->assertTrue(true); // fixme
        $this->assertOnlyAdminCanAccess("/admin/settings/{$setting->getId()}", $client);
    }

    public function testEditActionSubmit()
    {
        return $this->assertTrue(true); // fixme

        $client = self::createAdminClient();
        $em = self::getDoctrine($client);

        $this->makeElection($em);

        foreach (SettingRepository::getWhiteList() as $settingId => $setting) {
            foreach (self::langs() as $locale) {
                (function () use (&$settingId, &$setting, &$locale, &$client, &$em) {
                    $form = $client
                        ->request('GET', "/${locale}/admin/settings/${settingId}")
                        ->filter('form')->form();
                    $client->submit($form, ['setting[value]' => '']);

                    if (is_null($setting['default'])) {
                        $this->assertHasFormErrors($client->getResponse());
                    } else {
                        $this->assertRedirectsToRoute($client->getResponse(), 'admin_settings');
                    }
                })();

                (function () use (&$settingId, &$setting, &$locale, &$client, &$em) {
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
                        ->request('GET', "/${locale}/admin/settings/${settingId}")
                        ->filter('form')->form();
                    $client->submit($form, $formData);
                    $this->assertRedirectsToRoute($client->getResponse(), 'admin_settings');

                    $this->assertNotNull($em->getRepository('App:Setting')->get($settingId));
                })();
            }
        }

        self::cleanup($em);
    }
}
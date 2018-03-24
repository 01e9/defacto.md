<?php

namespace App\Tests\Controller;

use App\Entity\Status;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;
use Doctrine\Common\Persistence\ObjectManager;

class AdminStatusesControllerTest extends WebTestCase
{
    use TestCaseTrait;

    public function testAddActionAccess()
    {
        $client = static::createClient();
        $client->insulate();
        $this->assertTrue(self::onlyAdminCanAccess('/admin/statuses/add', $client));
    }

    public function testAddActionSubmit()
    {
        $client = static::createClient();
        $client->insulate();
        $client->followRedirects(false);
        self::logInClientAsRole($client, 'ROLE_ADMIN');

        $em = $client->getContainer()->get('doctrine.orm.default_entity_manager');
        $router = $client->getContainer()->get('router');

        $formData = [
            'status[name]' => 'Test',
            'status[namePlural]' => 'Tests',
            'status[slug]' => 'tests',
            'status[color]' => 'blue',
            'status[effect]' => '33',
        ];

        foreach (self::getLangs() as $lang) {
            (function () use (&$client, &$lang) {
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/statuses/add')
                    ->filter('form')->form();
                $client->submit($form, []);
                $response = $client->getResponse();
                $this->assertEquals(200, $response->getStatusCode());
                $this->assertContains('is-invalid', $response->getContent());
            })();

            (function () use (&$client, &$lang, &$formData, &$router, &$em) {
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/statuses/add')
                    ->filter('form')->form();
                $client->submit($form, $formData);
                $response = $client->getResponse();
                $this->assertEquals(302, $response->getStatusCode());

                $route = $router->match($response->getTargetUrl());
                $this->assertEquals('admin_status_edit', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                /** @var Status $status */
                $status = $em->getRepository('App:Status')->find($route['id']);

                $this->assertNotNull($status);
                $this->assertEquals($formData['status[name]'], $status->getName());
                $this->assertEquals($formData['status[color]'], $status->getColor());

                $em->remove($status);
                $em->flush();
            })();
        }

        $em->close();
        $em = null;
        static::$kernel->shutdown();
    }

    public function testEditActionAccess()
    {
        $client = static::createClient();
        $client->insulate();

        /** @var ObjectManager $manager */
        $manager = $client->getContainer()->get('doctrine')->getManager();
        $status = $manager->getRepository('App:Status')->findOneBy([]);

        $this->assertTrue(self::onlyAdminCanAccess('/admin/statuses/'. $status->getId(), $client));
    }

    public function testEditActionSubmit()
    {
        $client = static::createClient();
        $client->insulate();
        $client->followRedirects(false);
        self::logInClientAsRole($client, 'ROLE_ADMIN');

        $em = $client->getContainer()->get('doctrine.orm.default_entity_manager');
        $router = $client->getContainer()->get('router');

        $createStatus = function () use (&$em) : Status {
            $status = new Status();
            $status
                ->setName('Test')
                ->setNamePlural('Tests')
                ->setSlug('test')
                ->setEffect(30)
                ->setColor(null);
            $em->persist($status);
            $em->flush();

            return $status;
        };

        $formData = [
            'status[name]' => 'Test update',
            'status[namePlural]' => 'Test updates',
            'status[slug]' => 'test-updates',
            'status[color]' => 'blue',
            'status[effect]' => '33',
        ];

        foreach (self::getLangs() as $lang) {
            (function () use (&$client, &$lang, &$createStatus, &$em) {
                $status = $createStatus();
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/statuses/'. $status->getId())
                    ->filter('form')->form();
                $client->submit($form, [
                    'status[name]' => '?',
                ]);
                $response = $client->getResponse();

                $this->assertEquals(200, $response->getStatusCode());
                $this->assertContains('is-invalid', $response->getContent());

                $em->remove($status);
                $em->flush();
            })();

            (function () use (&$client, &$lang, &$createStatus, &$formData, &$em, &$router) {
                $status = $createStatus();
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/statuses/'. $status->getId())
                    ->filter('form')->form();
                $client->submit($form, $formData);
                $response = $client->getResponse();
                $this->assertEquals(302, $response->getStatusCode());

                $route = $router->match($response->getTargetUrl());
                $this->assertEquals('admin_promises', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                /** @var Status $status */
                $status = $em->getRepository('App:Status')->find($status->getId());

                $this->assertNotNull($status);

                $em->refresh($status);

                $this->assertEquals($formData['status[name]'], $status->getName());
                $this->assertEquals($formData['status[color]'], $status->getColor());

                $em->remove($status);
                $em->flush();
            })();
        }

        $em->close();
        $em = null;
        static::$kernel->shutdown();
    }
}
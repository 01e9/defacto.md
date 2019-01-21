<?php

namespace App\Tests\Controller;

use App\Entity\Party;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AdminPartiesControllerTest extends WebTestCase
{
    use TestCaseTrait;

    public function testIndexAction()
    {
        $client = static::createClient();
        $client->insulate();
        $this->assertTrue(self::onlyAdminCanAccess('/admin/parties', $client));
    }

    public function testAddActionAccess()
    {
        $client = static::createClient();
        $client->insulate();
        $this->assertTrue(self::onlyAdminCanAccess('/admin/parties/add', $client));
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
            'party[name]' => 'Test',
            'party[slug]' => 'test',
        ];

        foreach (self::getLangs() as $lang) {
            (function () use (&$client, &$lang) {
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/parties/add')
                    ->filter('form')->form();
                $client->submit($form, []);
                $response = $client->getResponse();

                $this->assertEquals(200, $response->getStatusCode());
                $this->assertContains('is-invalid', $response->getContent());
            })();

            (function () use (&$client, &$lang, &$em, &$router, &$formData) {
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/parties/add')
                    ->filter('form')->form();
                $client->submit($form, $formData);
                $response = $client->getResponse();

                $this->assertEquals(302, $response->getStatusCode());

                $route = $router->match($response->getTargetUrl());
                $this->assertEquals('admin_party_edit', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                /** @var Party $party */
                $party = $em->getRepository('App:Party')->find($route['id']);

                $this->assertNotNull($party);
                $this->assertEquals($formData['party[name]'], $party->getName());

                $em->remove($party);
                $em->flush();
            })();

            (function () use (&$client, &$lang, &$em, &$router, &$formData) {
                $logo = new UploadedFile(self::getTestsRootDir() . '/files/test.jpg', 'test.jpg');

                $form = $client
                    ->request('GET', '/'. $lang .'/admin/parties/add')
                    ->filter('form')->form();
                $client->insulate(false);
                $client->submit($form, array_merge($formData, [
                    'party[logo]' => $logo,
                ]));
                $client->insulate(true);
                $response = $client->getResponse();

                $this->assertEquals(302, $response->getStatusCode());

                $route = $router->match($response->getTargetUrl());
                $this->assertEquals('admin_party_edit', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                /** @var Party $party */
                $party = $em->getRepository('App:Party')->find($route['id']);
                $em->refresh($party); // fix lifecycle callbacks call

                $this->assertNotNull($party);
                $this->assertEquals($logo->getMimeType(), $party->getLogo()->getMimeType());

                $em->remove($party);
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
        $party = $this->createParty($manager);

        $this->assertTrue(self::onlyAdminCanAccess('/admin/parties/'. $party->getId(), $client));
    }

    public function testEditActionSubmit()
    {
        $client = static::createClient();
        $client->insulate();
        $client->followRedirects(false);
        self::logInClientAsRole($client, 'ROLE_ADMIN');

        $em = $client->getContainer()->get('doctrine.orm.default_entity_manager');
        $router = $client->getContainer()->get('router');

        $createParty = function() use (&$em) : Party {
            $party = new Party();
            $party
                ->setName('Test')
                ->setSlug('test');
            $em->persist($party);
            $em->flush();

            return $party;
        };

        $formData = [
            'party[name]' => 'Test',
            'party[slug]' => 'test',
        ];

        foreach (self::getLangs() as $lang) {
            (function () use (&$lang, &$client, &$createParty, &$em) {
                $party = $createParty();
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/parties/'. $party->getId())
                    ->filter('form')->form();
                $client->submit($form, [
                    'party[name]' => '?',
                ]);
                $response = $client->getResponse();

                $this->assertEquals(200, $response->getStatusCode());
                $this->assertContains('is-invalid', $response->getContent());

                $em->remove($party);
                $em->flush();
            })();

            (function () use (&$lang, &$client, &$createParty, &$em, &$formData, &$router) {
                $party = $createParty();
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/parties/'. $party->getId())
                    ->filter('form')->form();
                $client->submit($form, $formData);
                $response = $client->getResponse();

                $this->assertEquals(302, $response->getStatusCode());

                $route = $router->match($response->getTargetUrl());
                $this->assertEquals('admin_party_edit', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                $em->remove($party);
                $em->flush();
            })();

            (function () use (&$lang, &$client, &$createParty, &$em, &$formData, &$router) {
                $logo = new UploadedFile(self::getTestsRootDir() . '/files/test.gif', 'test.gif');

                $party = $createParty();
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/parties/'. $party->getId())
                    ->filter('form')->form();
                $client->insulate(false);
                $client->submit($form, array_merge($formData, [
                    'party[logo]' => $logo,
                ]));
                $client->insulate(true);
                $response = $client->getResponse();
                $this->assertEquals(302, $response->getStatusCode());

                $route = $router->match($response->getTargetUrl());
                $this->assertEquals('admin_party_edit', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                $party = $em->getRepository('App:Party')->find($party->getId());

                $this->assertNotNull($party);

                $em->refresh($party);

                $this->assertEquals($logo->getMimeType(), $party->getLogo()->getMimeType());

                $em->remove($party);
                $em->flush();
            })();
        }

        $em->close();
        $em = null;
        static::$kernel->shutdown();
    }

    public function testDeleteActionAccess()
    {
        $client = static::createClient();
        $client->insulate();

        /** @var ObjectManager $manager */
        $manager = $client->getContainer()->get('doctrine')->getManager();
        $party = $this->createParty($manager);

        $this->assertTrue(self::onlyAdminCanAccess('/admin/parties/'. $party->getId() .'/d', $client));

        $manager->remove($party);
        $manager->flush();
        $manager = null;
        $party = null;
        static::$kernel->shutdown();
    }

    public function testDeleteActionSubmit()
    {
        $client = static::createClient();
        $client->insulate();
        $client->followRedirects(false);
        self::logInClientAsRole($client, 'ROLE_ADMIN');

        /** @var ObjectManager $manager */
        $manager = $client->getContainer()->get('doctrine')->getManager();
        $router = $client->getContainer()->get('router');

        foreach (self::getLangs() as $lang) {
            $party = $this->createParty($manager);

            $form = $client
                ->request('GET', '/'. $lang .'/admin/parties/'. $party->getId() .'/d')
                ->filter('form')->form();
            $client->submit($form);
            $response = $client->getResponse();
            $this->assertEquals(302, $response->getStatusCode());

            $route = $router->match($response->getTargetUrl());
            $this->assertEquals('admin_parties', $route['_route']);
            $this->assertEquals($lang, $route['_locale']);

            $manager->clear('App:Party');

            /** @var Party $party */
            $party = $manager->getRepository('App:Party')->find($party->getId());

            $this->assertNull($party);
        }

        $manager = null;
        static::$kernel->shutdown();
    }
}
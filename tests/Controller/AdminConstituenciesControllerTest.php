<?php

namespace App\Tests\Controller;

use App\Entity\Constituency;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;
use Doctrine\Common\Persistence\ObjectManager;

class AdminConstituenciesControllerTest extends WebTestCase
{
    use TestCaseTrait;

    public function testAddActionAccess()
    {
        $client = static::createClient();
        $client->insulate();
        $this->assertTrue(self::onlyAdminCanAccess('/admin/constituencies/add', $client));
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
            'constituency[name]' => 'Test',
            'constituency[slug]' => 'tests',
            'constituency[link]' => 'https://test.test/test',
        ];

        foreach (self::getLangs() as $lang) {
            (function () use (&$client, &$lang) {
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/constituencies/add')
                    ->filter('form')->form();
                $client->submit($form, []);
                $response = $client->getResponse();
                $this->assertEquals(200, $response->getStatusCode());
                $this->assertContains('is-invalid', $response->getContent());
            })();

            (function () use (&$client, &$lang, &$formData, &$router, &$em) {
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/constituencies/add')
                    ->filter('form')->form();
                $client->submit($form, $formData);
                $response = $client->getResponse();
                $this->assertEquals(302, $response->getStatusCode());

                $route = $router->match($response->getTargetUrl());
                $this->assertEquals('admin_constituency_edit', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                /** @var Constituency $constituency */
                $constituency = $em->getRepository('App:Constituency')->find($route['id']);

                $this->assertNotNull($constituency);
                $this->assertEquals($formData['constituency[name]'], $constituency->getName());
                $this->assertEquals($formData['constituency[slug]'], $constituency->getSlug());

                $em->remove($constituency);
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
        $constituency = $this->createConstituency($manager);

        $this->assertTrue(self::onlyAdminCanAccess('/admin/constituencies/'. $constituency->getId(), $client));
    }

    public function testEditActionSubmit()
    {
        $client = static::createClient();
        $client->insulate();
        $client->followRedirects(false);
        self::logInClientAsRole($client, 'ROLE_ADMIN');

        $em = $client->getContainer()->get('doctrine.orm.default_entity_manager');
        $router = $client->getContainer()->get('router');

        $formData = [
            'constituency[name]' => 'Test constituency name',
            'constituency[slug]' => 'test-constituency-name',
            'constituency[link]' => 'https://test.test/test',
        ];

        foreach (self::getLangs() as $lang) {
            (function () use (&$client, &$lang, &$em) {
                $constituency = $this->createConstituency($em);
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/constituencies/'. $constituency->getId())
                    ->filter('form')->form();
                $client->submit($form, [
                    'constituency[name]' => '?',
                ]);
                $response = $client->getResponse();

                $this->assertEquals(200, $response->getStatusCode());
                $this->assertContains('is-invalid', $response->getContent());

                $em->remove($constituency);
                $em->flush();
            })();

            (function () use (&$client, &$lang, &$formData, &$em, &$router) {
                $constituency = $this->createConstituency($em);
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/constituencies/'. $constituency->getId())
                    ->filter('form')->form();
                $client->submit($form, $formData);
                $response = $client->getResponse();
                $this->assertEquals(302, $response->getStatusCode());

                $route = $router->match($response->getTargetUrl());
                $this->assertEquals('admin_constituencies', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                /** @var Constituency $constituency */
                $constituency = $em->getRepository('App:Constituency')->find($constituency->getId());

                $this->assertNotNull($constituency);

                $em->refresh($constituency);

                $this->assertEquals($formData['constituency[name]'], $constituency->getName());
                $this->assertEquals($formData['constituency[slug]'], $constituency->getSlug());

                $em->remove($constituency);
                $em->flush();
            })();

            (function () use (&$client, &$lang, &$formData, &$em, &$router) {
                $constituency = $this->createConstituency($em);
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/constituencies/'. $constituency->getId())
                    ->filter('form')->form();

                $formPhpValues = $form->getPhpValues();
                $formPhpValues['constituency']['problems'] = [
                    [
                        'constituency' => $constituency->getId(),
                        'election' => $this->createElection($em)->getId(),
                        'problem' => $this->createProblem($em)->getId(),
                        'respondents' => 123,
                    ],
                ];

                $client->request($form->getMethod(), $form->getUri(), $formPhpValues);
                $response = $client->getResponse();
                $this->assertEquals(302, $response->getStatusCode());

                $route = $router->match($response->getTargetUrl());
                $this->assertEquals('admin_constituencies', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                /** @var Constituency $constituency */
                $constituency = $em->getRepository('App:Constituency')->find($constituency->getId());

                $this->assertNotNull($constituency);

                $em->refresh($constituency);

                $this->assertEquals(
                    $formPhpValues['constituency']['problems'][0]['respondents'],
                    $constituency->getProblems()->first()->getRespondents()
                );

                $em->remove($constituency);
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
        $constituency = $this->createConstituency($manager);

        $this->assertTrue(self::onlyAdminCanAccess('/admin/constituencies/'. $constituency->getId() .'/d', $client));

        $manager->remove($constituency);
        $manager->flush();
        $manager = null;
        $constituency = null;
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
            $constituency = $this->createConstituency($manager);

            $form = $client
                ->request('GET', '/'. $lang .'/admin/constituencies/'. $constituency->getId() .'/d')
                ->filter('form')->form();
            $client->submit($form);
            $response = $client->getResponse();
            $this->assertEquals(302, $response->getStatusCode());

            $route = $router->match($response->getTargetUrl());
            $this->assertEquals('admin_constituencies', $route['_route']);
            $this->assertEquals($lang, $route['_locale']);

            $manager->clear('App:Constituency');

            /** @var Constituency $constituency */
            $constituency = $manager->getRepository('App:Constituency')->find($constituency->getId());

            $this->assertNull($constituency);
        }

        $manager = null;
        static::$kernel->shutdown();
    }
}
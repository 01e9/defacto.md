<?php

namespace App\Tests\Controller;

use App\Entity\Problem;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;
use Doctrine\Common\Persistence\ObjectManager;

class AdminProblemsControllerTest extends WebTestCase
{
    use TestCaseTrait;

    public function testAddActionAccess()
    {
        $client = static::createClient();
        $client->insulate();
        $this->assertTrue(self::onlyAdminCanAccess('/admin/problems/add', $client));
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
            'problem[name]' => 'Test',
            'problem[slug]' => 'tests',
        ];

        foreach (self::getLangs() as $lang) {
            (function () use (&$client, &$lang) {
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/problems/add')
                    ->filter('form')->form();
                $client->submit($form, []);
                $response = $client->getResponse();
                $this->assertEquals(200, $response->getStatusCode());
                $this->assertContains('is-invalid', $response->getContent());
            })();

            (function () use (&$client, &$lang, &$formData, &$router, &$em) {
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/problems/add')
                    ->filter('form')->form();
                $client->submit($form, $formData);
                $response = $client->getResponse();
                $this->assertEquals(302, $response->getStatusCode());

                $route = $router->match($response->getTargetUrl());
                $this->assertEquals('admin_problem_edit', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                /** @var Problem $problem */
                $problem = $em->getRepository('App:Problem')->find($route['id']);

                $this->assertNotNull($problem);
                $this->assertEquals($formData['problem[name]'], $problem->getName());
                $this->assertEquals($formData['problem[slug]'], $problem->getSlug());

                $em->remove($problem);
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
        $problem = $this->createProblem($manager);

        $this->assertTrue(self::onlyAdminCanAccess('/admin/problems/'. $problem->getId(), $client));
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
            'problem[name]' => 'Test problem name',
            'problem[slug]' => 'test-problem-name',
        ];

        foreach (self::getLangs() as $lang) {
            (function () use (&$client, &$lang, &$em) {
                $problem = $this->createProblem($em);
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/problems/'. $problem->getId())
                    ->filter('form')->form();
                $client->submit($form, [
                    'problem[name]' => '?',
                ]);
                $response = $client->getResponse();

                $this->assertEquals(200, $response->getStatusCode());
                $this->assertContains('is-invalid', $response->getContent());

                $em->remove($problem);
                $em->flush();
            })();

            (function () use (&$client, &$lang, &$formData, &$em, &$router) {
                $problem = $this->createProblem($em);
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/problems/'. $problem->getId())
                    ->filter('form')->form();
                $client->submit($form, $formData);
                $response = $client->getResponse();
                $this->assertEquals(302, $response->getStatusCode());

                $route = $router->match($response->getTargetUrl());
                $this->assertEquals('admin_problem_edit', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                /** @var Problem $problem */
                $problem = $em->getRepository('App:Problem')->find($problem->getId());

                $this->assertNotNull($problem);

                $em->refresh($problem);

                $this->assertEquals($formData['problem[name]'], $problem->getName());
                $this->assertEquals($formData['problem[slug]'], $problem->getSlug());

                $em->remove($problem);
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
        $problem = $this->createProblem($manager);

        $this->assertTrue(self::onlyAdminCanAccess('/admin/problems/'. $problem->getId() .'/d', $client));

        $manager->remove($problem);
        $manager->flush();
        $manager = null;
        $problem = null;
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
            $problem = $this->createProblem($manager);

            $form = $client
                ->request('GET', '/'. $lang .'/admin/problems/'. $problem->getId() .'/d')
                ->filter('form')->form();
            $client->submit($form);
            $response = $client->getResponse();
            $this->assertEquals(302, $response->getStatusCode());

            $route = $router->match($response->getTargetUrl());
            $this->assertEquals('admin_problems', $route['_route']);
            $this->assertEquals($lang, $route['_locale']);

            $manager->clear('App:Problem');

            /** @var Problem $problem */
            $problem = $manager->getRepository('App:Problem')->find($problem->getId());

            $this->assertNull($problem);
        }

        $manager = null;
        static::$kernel->shutdown();
    }
}
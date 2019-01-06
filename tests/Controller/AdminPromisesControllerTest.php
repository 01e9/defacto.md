<?php

namespace App\Tests\Controller;

use App\Consts;
use App\Entity\Promise;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;
use Doctrine\Common\Persistence\ObjectManager;

class AdminPromisesControllerTest extends WebTestCase
{
    use TestCaseTrait;

    public function testIndexAction()
    {
        $client = static::createClient();
        $client->insulate();
        $this->assertTrue(self::onlyAdminCanAccess('/admin/promises', $client));
    }

    public function testAddActionAccess()
    {
        $client = static::createClient();
        $client->insulate();
        $this->assertTrue(self::onlyAdminCanAccess('/admin/promises/add', $client));
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
            'promise[name]' => 'Test',
            'promise[slug]' => 'test',
            'promise[description]' => 'Test',
            'promise[madeTime]' => (new \DateTime())->format(Consts::DATE_FORMAT_PHP),
            'promise[status]' => $em->getRepository('App:Status')->findOneBy([])->getId(),
            'promise[election]' => $this->createElection($em)->getId(),
            'promise[politician]' => $this->createPolitician($em)->getId(),
            'promise[categories]' => [
                $em->getRepository('App:Category')->findOneBy([])->getId()
            ],
            'promise[published]' => true,
        ];

        foreach (self::getLangs() as $lang) {
            (function () use (&$client, &$lang) {
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/promises/add')
                    ->filter('form')->form();
                $client->submit($form, []);
                $response = $client->getResponse();

                $this->assertEquals(200, $response->getStatusCode());
                $this->assertContains('is-invalid', $response->getContent());
            })();

            (function () use (&$client, &$lang, &$formData, &$router, &$em) {
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/promises/add')
                    ->filter('form')->form();
                $client->submit($form, $formData);
                $response = $client->getResponse();

                $this->assertEquals(302, $response->getStatusCode());

                $route = $router->match($response->getTargetUrl());
                $this->assertEquals('admin_promise_edit', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                /** @var Promise $promise */
                $promise = $em->getRepository('App:Promise')->find($route['id']);

                $this->assertNotNull($promise);
                $this->assertEquals(1, $promise->getCategories()->count());
                $this->assertEquals($formData['promise[categories]'][0], $promise->getCategories()->first()->getId());
                $this->assertEquals($formData['promise[status]'], $promise->getStatus()->getId());

                $em->remove($promise);
                $em->flush();
            })();

            (function () use (&$client, &$lang, &$formData, &$router, &$em) {
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/promises/add')
                    ->filter('form')->form();
                $client->submit($form, array_merge($formData, [
                    'promise[status]' => '',
                ]));
                $response = $client->getResponse();
                $this->assertEquals(302, $response->getStatusCode());

                $route = $router->match($response->getTargetUrl());
                $this->assertEquals('admin_promise_edit', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                $promise = $em->getRepository('App:Promise')->find($route['id']);

                $this->assertNotNull($promise);
                $this->assertEquals(null, $promise->getStatus());

                $em->remove($promise);
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
        $promise = $manager->getRepository('App:Promise')->findOneBy([]);

        $this->assertTrue(self::onlyAdminCanAccess('/admin/promises/'. $promise->getId(), $client));
    }

    public function testEditActionSubmit()
    {
        $client = static::createClient();
        $client->insulate();
        $client->followRedirects(false);
        self::logInClientAsRole($client, 'ROLE_ADMIN');

        $em = $client->getContainer()->get('doctrine.orm.default_entity_manager');
        $router = $client->getContainer()->get('router');

        $createPromise = function () use (&$em) : Promise {
            $promise = new Promise();
            $promise
                ->setName('Test')
                ->setSlug('test')
                ->setDescription('Test')
                ->setMadeTime(new \DateTime())
                ->setStatus(null)
                ->setElection($this->createElection($em))
                ->setPolitician($this->createPolitician($em))
                ->setPublished(true);
            $em->persist($promise);
            $em->flush();

            return $promise;
        };

        $createFormData = function (Promise $promise) use (&$em) {
            return [
                'promise[name]' => 'Test',
                'promise[slug]' => 'test',
                'promise[description]' => 'Test',
                'promise[madeTime]' => (new \DateTime())->format(Consts::DATE_FORMAT_PHP),
                'promise[status]' => $em->getRepository('App:Status')->findOneBy([])->getId(),
                'promise[election]' => $this->createElection($em)->getId(),
                'promise[politician]' => $this->createPolitician($em)->getId(),
                'promise[categories]' => [
                    $em->getRepository('App:Category')->findOneBy([])->getId()
                ],
                'promise[published]' => true,
                'promise[sources]' => [
                    [
                        'promise' => $promise->getId(),
                        'name' => 'Source name',
                        'link' => 'https://source.link'
                    ],
                ],
            ];
        };

        foreach (self::getLangs() as $lang) {
            (function () use (&$client, &$lang, &$createPromise, &$em) {
                $promise = $createPromise();
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/promises/'. $promise->getId())
                    ->filter('form')->form();
                $client->submit($form, [
                    'promise[name]' => '',
                ]);
                $response = $client->getResponse();

                $this->assertEquals(200, $response->getStatusCode());
                $this->assertContains('is-invalid', $response->getContent());

                $em->remove($promise);
                $em->flush();
            })();

            (function () use (&$client, &$lang, &$createPromise, &$createFormData, &$router, &$em) {
                $promise = $createPromise();
                $formData = $createFormData($promise);
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/promises/'. $promise->getId())
                    ->filter('form')->form();
                $client->submit($form, array_diff_key($formData, ['promise[sources]' => false]));
                $response = $client->getResponse();

                $this->assertEquals(302, $response->getStatusCode());

                $route = $router->match($response->getTargetUrl());
                $this->assertEquals('admin_promise_edit', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                /** @var Promise $promise */
                $promise = $em->getRepository('App:Promise')->find($promise->getId());

                $this->assertNotNull($promise);

                $em->refresh($promise);

                $this->assertEquals(1, $promise->getCategories()->count());
                $this->assertEquals($formData['promise[categories]'][0], $promise->getCategories()->first()->getId());
                $this->assertEquals($formData['promise[status]'], $promise->getStatus()->getId());

                $em->remove($promise);
                $em->flush();
            })();

            (function () use (&$client, &$lang, &$createPromise, &$createFormData, &$router, &$em) {
                $promise = $createPromise();
                $formData = $createFormData($promise);
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/promises/'. $promise->getId())
                    ->filter('form')->form();
                $formPhpValues = $form->getPhpValues();
                $formPhpValues['promise']['sources'] = $formData['promise[sources]'];
                $formPhpValues['promise']['politician'] = $formData['promise[politician]']; // fixme: why it's empty?

                $client->request($form->getMethod(), $form->getUri(), $formPhpValues);
                $response = $client->getResponse();

                $this->assertEquals(302, $response->getStatusCode());

                $route = $router->match($response->getTargetUrl());
                $this->assertEquals('admin_promise_edit', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                /** @var Promise $promise */
                $promise = $em->getRepository('App:Promise')->find($promise->getId());

                $this->assertNotNull($promise);

                $em->refresh($promise);

                $this->assertCount(count($formData['promise[sources]']), $promise->getSources());

                $em->remove($promise);
                $em->flush();
            })();

            (function () use (&$client, &$lang, &$createPromise, &$createFormData, &$router, &$em) {
                $promise = $createPromise();
                $formData = $createFormData($promise);
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/promises/'. $promise->getId())
                    ->filter('form')->form();
                $client->submit($form, array_merge(array_diff_key($formData, ['promise[sources]' => false]), [
                    'promise[status]' => '',
                ]));
                $response = $client->getResponse();
                $this->assertEquals(302, $response->getStatusCode());

                $route = $router->match($response->getTargetUrl());
                $this->assertEquals('admin_promise_edit', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                /** @var Promise $promise */
                $promise = $em->getRepository('App:Promise')->find($promise->getId());

                $this->assertNotNull($promise);

                $em->refresh($promise);

                $this->assertEquals(null, $promise->getStatus());

                $em->remove($promise);
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
        $promise = $this->createPromise($manager);

        $this->assertTrue(self::onlyAdminCanAccess('/admin/promises/'. $promise->getId() .'/d', $client));

        $manager->remove($promise);
        $manager->flush();
        $manager = null;
        $promise = null;
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
            $promise = $this->createPromise($manager);

            $form = $client
                ->request('GET', '/'. $lang .'/admin/promises/'. $promise->getId() .'/d')
                ->filter('form')->form();
            $client->submit($form);
            $response = $client->getResponse();
            $this->assertEquals(302, $response->getStatusCode());

            $route = $router->match($response->getTargetUrl());
            $this->assertEquals('admin_promises', $route['_route']);
            $this->assertEquals($lang, $route['_locale']);

            $manager->clear('App:Promise');

            /** @var Promise $promise */
            $promise = $manager->getRepository('App:Promise')->find($promise->getId());

            $this->assertNull($promise);
        }

        $manager = null;
        static::$kernel->shutdown();
    }
}
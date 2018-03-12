<?php

namespace App\Tests\Controller;

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
        $this->assertTrue(self::onlyAdminCanAccess('/admin/promises', $client));
    }

    public function testAddActionAccess()
    {
        $client = static::createClient();
        $this->assertTrue(self::onlyAdminCanAccess('/admin/promises/add', $client));
    }

    public function testAddActionSubmit()
    {
        $client = static::createClient();
        $client->followRedirects(false);
        self::logInClientAsRole($client, 'ROLE_ADMIN');

        $em = $client->getContainer()->get('doctrine.orm.default_entity_manager');
        $router = $client->getContainer()->get('router');

        $formData = [
            'promise[name]' => 'Test',
            'promise[slug]' => 'test',
            'promise[description]' => 'Test',
            'promise[madeTime]' => (new \DateTime())->format('Y-m-d'),
            'promise[status]' => $em->getRepository('App:Status')->findOneBy([])->getId(),
            'promise[mandate]' => $em->getRepository('App:Mandate')->findOneBy([])->getId(),
            'promise[categories]' => [
                $em->getRepository('App:Category')->findOneBy([])->getId()
            ],
            'promise[published]' => true,
        ];

        foreach (self::getLangs() as $lang) {
            $form = $client
                ->request('GET', '/'. $lang .'/admin/promises/add')
                ->filter('form')->form();

            {
                $client->submit($form, []);
                $response = $client->getResponse();
                $this->assertEquals(200, $response->getStatusCode());
                $this->assertContains('is-invalid', $response->getContent());
            }

            {
                $client->submit($form, $formData);
                $response = $client->getResponse();
                $this->assertEquals(302, $response->getStatusCode());

                $route = $router->match($response->getTargetUrl());
                $this->assertEquals('admin_promise_edit', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                /** @var Promise $promise */
                $promise = $em->getRepository('App:Promise')->find($route['id']);

                $this->assertEquals(1, $promise->getCategories()->count());
                $this->assertEquals($formData['promise[categories]'][0], $promise->getCategories()->first()->getId());
                $this->assertEquals($formData['promise[status]'], $promise->getStatus()->getId());

                $em->remove($promise);
                $em->flush();
            }

            {
                $client->submit($form, array_merge($formData, [
                    'promise[status]' => '',
                ]));
                $response = $client->getResponse();
                $this->assertEquals(302, $response->getStatusCode());

                $route = $router->match($response->getTargetUrl());
                $this->assertEquals('admin_promise_edit', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                $promise = $em->getRepository('App:Promise')->find($route['id']);

                $this->assertEquals(null, $promise->getStatus());

                $em->remove($promise);
                $em->flush();
            }
        }

        $em->close();
        $em = null;
        static::$kernel->shutdown();
    }

    public function testEditActionAccess()
    {
        $client = static::createClient();

        /** @var ObjectManager $manager */
        $manager = $client->getContainer()->get('doctrine')->getManager();
        $promise = $manager->getRepository('App:Promise')->findOneBy([]);

        $this->assertTrue(self::onlyAdminCanAccess('/admin/promises/'. $promise->getId(), $client));
    }

    public function testEditActionSubmit()
    {
        $client = static::createClient();
        $client->followRedirects(false);
        self::logInClientAsRole($client, 'ROLE_ADMIN');

        $em = $client->getContainer()->get('doctrine.orm.default_entity_manager');
        $router = $client->getContainer()->get('router');

        $promise = new Promise();
        $promise
            ->setName('Test')
            ->setSlug('test')
            ->setDescription('Test')
            ->setMadeTime(new \DateTime())
            ->setStatus(null)
            ->setMandate($em->getRepository('App:Mandate')->findOneBy([]))
            ->setPublished(true);
        $em->persist($promise);
        $em->flush();

        $formData = [
            'promise[name]' => 'Test',
            'promise[slug]' => 'test',
            'promise[description]' => 'Test',
            'promise[madeTime]' => (new \DateTime())->format('Y-m-d'),
            'promise[status]' => $em->getRepository('App:Status')->findOneBy([])->getId(),
            'promise[mandate]' => $promise->getMandate()->getId(),
            'promise[categories]' => [
                $em->getRepository('App:Category')->findOneBy([])->getId()
            ],
            'promise[published]' => true,
        ];

        foreach (self::getLangs() as $lang) {
            $form = $client
                ->request('GET', '/'. $lang .'/admin/promises/'. $promise->getId())
                ->filter('form')->form();

            {
                $client->submit($form, [
                    'promise[name]' => '',
                ]);
                $response = $client->getResponse();
                $this->assertEquals(200, $response->getStatusCode());
                $this->assertContains('is-invalid', $response->getContent());
            }

            {
                $client->submit($form, $formData);
                $response = $client->getResponse();
                $this->assertEquals(302, $response->getStatusCode());

                $route = $router->match($response->getTargetUrl());
                $this->assertEquals('admin_promises', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                /** @var Promise $promise */
                $promise = $em->getRepository('App:Promise')->find($promise->getId());
                $em->refresh($promise);

                $this->assertEquals(1, $promise->getCategories()->count());
                $this->assertEquals($formData['promise[categories]'][0], $promise->getCategories()->first()->getId());
                $this->assertEquals($formData['promise[status]'], $promise->getStatus()->getId());
            }

            {
                $client->submit($form, array_merge($formData, [
                    'promise[status]' => '',
                ]));
                $response = $client->getResponse();
                $this->assertEquals(302, $response->getStatusCode());

                $route = $router->match($response->getTargetUrl());
                $this->assertEquals('admin_promises', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                /** @var Promise $promise */
                $promise = $em->getRepository('App:Promise')->find($promise->getId());
                $em->refresh($promise);

                $this->assertEquals(null, $promise->getStatus());
            }
        }

        $em->remove($promise);
        $em->flush();

        $em->close();
        $em = null;
        static::$kernel->shutdown();
    }
}
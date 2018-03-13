<?php

namespace App\Tests\Controller;

use App\Entity\Action;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;
use Doctrine\Common\Persistence\ObjectManager;

class AdminActionsControllerTest extends WebTestCase
{
    use TestCaseTrait;

    public function testIndexAction()
    {
        $client = static::createClient();
        $client->insulate();
        $this->assertTrue(self::onlyAdminCanAccess('/admin/actions', $client));
    }

    public function testAddActionAccess()
    {
        $client = static::createClient();
        $client->insulate();
        $this->assertTrue(self::onlyAdminCanAccess('/admin/actions/add', $client));
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
            'action[name]' => 'Test',
            'action[slug]' => 'test',
            'action[description]' => 'Test',
            'action[occurredTime]' => (new \DateTime())->format('Y-m-d'),
            'action[published]' => true,
            'action[mandate]' => $em->getRepository('App:Mandate')->findOneBy([])->getId(),
        ];

        foreach (self::getLangs() as $lang) {
            $form = $client
                ->request('GET', '/'. $lang .'/admin/actions/add')
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
                $this->assertEquals('admin_action_edit', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                $action = $em->getRepository('App:Action')->find($route['id']);

                $this->assertNotNull($action);

                $em->remove($action);
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
        $client->insulate();

        /** @var ObjectManager $manager */
        $manager = $client->getContainer()->get('doctrine')->getManager();
        $action = $manager->getRepository('App:Action')->findOneBy([]);

        $this->assertTrue(self::onlyAdminCanAccess('/admin/actions/'. $action->getId(), $client));
    }

    public function testEditActionSubmit()
    {
        $client = static::createClient();
        $client->insulate();
        $client->followRedirects(false);
        self::logInClientAsRole($client, 'ROLE_ADMIN');

        $em = $client->getContainer()->get('doctrine.orm.default_entity_manager');
        $router = $client->getContainer()->get('router');

        $action = new Action();
        $action
            ->setName('Test')
            ->setSlug('test')
            ->setDescription('Test')
            ->setOccurredTime(new \DateTime())
            ->setPublished(true)
            ->setMandate($em->getRepository('App:Mandate')->findOneBy([]));
        $em->persist($action);
        $em->flush();

        $formData = [
            'action[name]' => 'Updated',
            'action[slug]' => 'updated',
            'action[description]' => 'Updated',
            'action[occurredTime]' => (new \DateTime())->format('Y-m-d'),
            'action[published]' => true,
            'action[mandate]' => $action->getMandate()->getId(),
            'action[statusUpdates]' => [
                [
                    'action' => $action->getId(),
                    'promise' => $em->getRepository('App:Promise')->findOneBy([
                        'mandate' => $action->getMandate()->getId(),
                    ])->getId(),
                    'status' => $em->getRepository('App:Status')->findOneBy([])->getId(),
                ]
            ],
        ];

        foreach (self::getLangs() as $lang) {
            $form = $client
                ->request('GET', '/'. $lang .'/admin/actions/'. $action->getId())
                ->filter('form')->form();

            {
                $client->submit($form, [
                    'action[name]' => '',
                ]);
                $response = $client->getResponse();
                $this->assertEquals(200, $response->getStatusCode());
                $this->assertContains('is-invalid', $response->getContent());
            }

            {
                $client->submit($form, array_diff_key($formData, ['action[statusUpdates]' => false]));
                $response = $client->getResponse();
                $this->assertEquals(302, $response->getStatusCode());

                $route = $router->match($response->getTargetUrl());
                $this->assertEquals('admin_actions', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                $action = $em->getRepository('App:Action')->find($action->getId());

                $this->assertNotNull($action);

                $em->refresh($action);

                $this->assertEquals('Updated', $action->getName());
                $this->assertCount(0, $action->getStatusUpdates());
            }

            {
                $formPhpValues = $form->getPhpValues();
                $formPhpValues['action']['statusUpdates'] = $formData['action[statusUpdates]'];

                $client->request($form->getMethod(), $form->getUri(), $formPhpValues);

                $response = $client->getResponse();
                $this->assertEquals(302, $response->getStatusCode());

                $route = $router->match($response->getTargetUrl());
                $this->assertEquals('admin_actions', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                $action = $em->getRepository('App:Action')->find($action->getId());

                $this->assertNotNull($action);

                $em->refresh($action);

                $this->assertCount(count($formData['action[statusUpdates]']), $action->getStatusUpdates());
            }
        }

        $em->remove($action);
        $em->flush();

        $em->close();
        $em = null;
        static::$kernel->shutdown();
    }
}
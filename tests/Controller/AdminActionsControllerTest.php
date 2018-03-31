<?php

namespace App\Tests\Controller;

use App\Entity\Action;
use App\Entity\StatusUpdate;
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
            (function () use (&$em, &$client, &$lang) {
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/actions/add')
                    ->filter('form')->form();
                $client->submit($form, []);
                $response = $client->getResponse();

                $this->assertEquals(200, $response->getStatusCode());
                $this->assertContains('is-invalid', $response->getContent());
            })();

            (function () use (&$em, &$client, &$lang, &$formData, &$router) {
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/actions/add')
                    ->filter('form')->form();
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

        $createAction = function () use (&$em) : Action {
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

            return $action;
        };

        $createFormData = function (Action $action) use (&$em) {
            $promises = $em->getRepository('App:Promise')->findBy([
                'mandate' => $action->getMandate()->getId(),
            ], null, 2);
            $this->assertCount(2, $promises);

            $powers = $action->getMandate()->getInstitutionTitle()->getTitle()->getPowers();
            $this->assertGreaterThanOrEqual(2, count($powers));

            return [
                'action[name]' => 'Updated',
                'action[slug]' => 'updated',
                'action[description]' => 'Updated',
                'action[occurredTime]' => (new \DateTime())->format('Y-m-d'),
                'action[published]' => true,
                'action[mandate]' => $action->getMandate()->getId(),
                'action[usedPowers]' => [
                    $powers[0]->getId(),
                    $powers[1]->getId(),
                ],
                'action[statusUpdates]' => [
                    [
                        'action' => $action->getId(),
                        'promise' => $promises[0]->getId(),
                        'status' => $em->getRepository('App:Status')->findOneBy([])->getId(),
                    ],
                    [
                        'action' => $action->getId(),
                        'promise' => $promises[1]->getId(),
                        'status' => '',
                    ],
                ],
            ];
        };

        foreach (self::getLangs() as $lang) {
            (function () use (&$em, &$client, &$lang, &$createAction) {
                $action = $createAction();
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/actions/'. $action->getId())
                    ->filter('form')->form();
                $client->submit($form, ['action[name]' => '']);
                $response = $client->getResponse();

                $this->assertEquals(200, $response->getStatusCode());
                $this->assertContains('is-invalid', $response->getContent());

                $em->remove($action);
                $em->flush();
            })();

            (function () use (&$em, &$client, &$lang, &$createAction, &$createFormData, &$router) {
                $action = $createAction();
                $formData = $createFormData($action);
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/actions/'. $action->getId())
                    ->filter('form')->form();
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

                $em->remove($action);
                $em->flush();
            })();

            (function () use (&$em, &$client, &$lang, &$createAction, &$createFormData, &$router) {
                $action = $createAction();
                $formData = $createFormData($action);
                $form = $client
                    ->request('GET', '/'. $lang .'/admin/actions/'. $action->getId())
                    ->filter('form')->form();
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

                array_map(function (StatusUpdate $statusUpdate) use (&$em) {
                    $em->remove($statusUpdate);
                    $em->flush();
                }, $action->getStatusUpdates()->toArray());

                $em->remove($action);
                $em->flush();
            })();
        }

        $em->close();
        $em = null;
        static::$kernel->shutdown();
    }
}
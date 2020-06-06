<?php

namespace App\Tests\Controller;

use App\Consts;
use App\Entity\PromiseAction;
use App\Entity\PromiseActionSource;
use App\Entity\Mandate;
use App\Entity\Power;
use App\Entity\PromiseUpdate;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;
use Doctrine\Common\Persistence\ObjectManager;

class AdminActionsControllerTest extends WebTestCase
{
    use TestCaseTrait;

    //region Add

    private function createAddFormData(Mandate $mandate)
    {
        $random = self::randomNumber();

        return [
            'action[name]' => "Test ${random}",
            'action[slug]' => "test-${random}",
            'action[description]' => "Test ${random}",
            'action[occurredTime]' => (new \DateTime())->format(Consts::DATE_FORMAT_PHP),
            'action[published]' => true,
            'action[mandate]' => $mandate->getId(),
        ];
    }

    public function testAddActionAccess()
    {
        $this->assertOnlyAdminCanAccess('/admin/actions/add');
    }

    public function testAddActionSubmitEmptyData()
    {
        $client = self::createAdminClient();
        $locale = self::getLocale($client);
        $form = $client
            ->request('GET', "/{$locale}/admin/actions/add")
            ->filter('form')->form();
        $client->submit($form, []);
        $this->assertHasFormErrors($client->getResponse());
    }

    public function testAddActionSubmitValidData()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);

        $mandate = $this->makeMandate($em);

        $locale = self::getLocale($client);
        $form = $client
            ->request('GET', "/${locale}/admin/actions/add")
            ->filter('form')->form();
        $client->submit($form, $this->createAddFormData($mandate));
        $route = $this->assertRedirectsToRoute($client->getResponse(), 'admin_action_edit');

        $action = $em->getRepository('App:PromiseAction')->find($route['id']);

        $this->assertNotNull($action);

        self::cleanup($em);
    }

    public function testAddActionSubmitValidDataWithPromiseQueryParam()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $mandate = $this->makeMandate($em);

        $promise = $this->makePromise($em);
        $promise->setPolitician($mandate->getPolitician());
        $promise->setElection($mandate->getElection());

        $form = $client
            ->request(
                'GET',
                "/${locale}/admin/actions/add?". http_build_query(['promise' => $promise->getId()])
            )
            ->filter('form')->form();
        $client->submit($form, $this->createAddFormData($mandate));
        $route = $this->assertRedirectsToRoute($client->getResponse(), 'admin_action_edit');

        $action = $em->getRepository('App:PromiseAction')->find($route['id']);

        $this->assertNotNull($action);
        $this->assertNotEmpty($action->getPromiseUpdates());
        $this->assertEquals($promise->getId(), $action->getPromiseUpdates()->first()->getPromise()->getId());

        self::cleanup($em);
    }

    //endregion

    //region Edit

    private function createEditFormData(PromiseAction $action) : array
    {
        $data = [
            'action[name]' => $action->getName(),
            'action[slug]' => $action->getSlug(),
            'action[description]' => $action->getDescription(),
            'action[occurredTime]' => $action->getOccurredTime()->format(Consts::DATE_FORMAT_PHP),
            'action[published]' => $action->getPublished(),
            'action[mandate]' => $action->getMandate()->getId(),
        ];

        if (!$action->getUsedPowers()->isEmpty()) {
            $data['action[usedPowers]'] = array_map(
                function (Power $power) {
                    return $power->getId();
                },
                $action->getUsedPowers()->toArray()
            );
        }
        if (!$action->getPromiseUpdates()->isEmpty()) {
            $data['action[promiseUpdates]'] = array_map(
                function (PromiseUpdate $promiseUpdate) {
                    return [
                        'action' => $promiseUpdate->getAction()->getId(),
                        'promise' => $promiseUpdate->getPromise()->getId(),
                        'status' => $promiseUpdate->getStatus() ? $promiseUpdate->getStatus()->getId() : '',
                    ];
                },
                $action->getPromiseUpdates()->toArray()
            );
        }
        if (!$action->getSources()->isEmpty()) {
            $data['action[sources]'] = array_map(
                function (PromiseActionSource $source) {
                    return [
                        'action' => $source->getAction()->getId(),
                        'name' => $source->getName(),
                        'link' => $source->getLink(),
                    ];
                },
                $action->getSources()->toArray()
            );
        }

        return $data;
    }

    public function testEditActionAccess()
    {
        $client = static::createClient();
        $client->insulate();

        /** @var ObjectManager $manager */
        $manager = $client->getContainer()->get('doctrine')->getManager();
        $action = $this->makePromiseAction($manager);

        $this->assertOnlyAdminCanAccess("/admin/actions/{$action->getId()}", $client);
    }

    public function testEditActionSubmitEmptyData()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);
        $formData = ['action[name]' => ''];

        $action = $this->makePromiseAction($em);
        $form = $client
            ->request('GET', "/${locale}/admin/actions/{$action->getId()}")
            ->filter('form')->form();
        $client->submit($form, $formData);

        $this->assertHasFormErrors($client->getResponse());

        $em->remove($action);
        $em->flush();

        self::cleanup($em);
    }

    public function testEditActionSubmitSameData()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);
        $action = $this->makePromiseAction($em);
        $formData = $this->createEditFormData($action);

        $form = $client
            ->request('GET', "/${locale}/admin/actions/{$action->getId()}")
            ->filter('form')->form();
        $client->submit($form, $formData);

        $this->assertRedirectsToRoute($client->getResponse(), 'admin_action_edit');

        self::cleanup($em);
    }

    public function testEditActionSubmitPromiseUpdatesAdded()
    {
        $client = self::createAdminClient();

        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $action = $this->makePromiseAction($em);
        $this->assertCount(0, $action->getPromiseUpdates());

        $form = $client
            ->request('GET', "/${locale}/admin/actions/{$action->getId()}")
            ->filter('form')->form();

        $formPhpValues = $form->getPhpValues();

        {
            $promise1 = $this->makePromise($em);
            $promise1->setElection($action->getMandate()->getElection());
            $promise1->setPolitician($action->getMandate()->getPolitician());
            $em->flush();

            $promise2 = $this->makePromise($em);
            $promise2->setElection($action->getMandate()->getElection());
            $promise2->setPolitician($action->getMandate()->getPolitician());
            $em->flush();

            $status = $this->makeStatus($em);
        }
        $formPhpValues['action']['promiseUpdates'] = [
            [
                'action' => $action->getId(),
                'promise' => $promise1->getId(),
                'status' => '',
            ],
            [
                'action' => $action->getId(),
                'promise' => $promise2->getId(),
                'status' => $status->getId(),
            ],
        ];

        $client->request($form->getMethod(), $form->getUri(), $formPhpValues);
        $this->assertRedirectsToRoute($client->getResponse(), 'admin_action_edit');

        $em->clear('App:PromiseAction');
        $action = $em->getRepository('App:PromiseAction')->find($action->getId());

        $this->assertNotNull($action);
        $this->assertCount(2, $action->getPromiseUpdates());

        self::cleanup($em);
    }

    public function testEditActionSubmitPromiseUpdatesRemoved()
    {
        $client = self::createAdminClient();

        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $action = $this->makePromiseAction($em);

        $action->setPromiseUpdates(new ArrayCollection([$this->makePromiseUpdate($em, $action)]));
        $em->flush();

        $form = $client
            ->request('GET', "/${locale}/admin/actions/{$action->getId()}")
            ->filter('form')->form();

        $formPhpValues = $form->getPhpValues();
        unset($formPhpValues['action']['promiseUpdates']);

        $client->request($form->getMethod(), $form->getUri(), $formPhpValues);
        $this->assertRedirectsToRoute($client->getResponse(), 'admin_action_edit');

        $em->clear('App:PromiseAction');
        $action = $em->getRepository('App:PromiseAction')->find($action->getId());

        $this->assertNotNull($action);
        $this->assertCount(0, $action->getPromiseUpdates());

        self::cleanup($em);
    }

    public function testEditActionSubmitSourcesAdded()
    {
        $client = self::createAdminClient();

        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $action = $this->makePromiseAction($em);
        $this->assertCount(0, $action->getSources());

        $form = $client
            ->request('GET', "/${locale}/admin/actions/{$action->getId()}")
            ->filter('form')->form();

        $formPhpValues = $form->getPhpValues();

        $formPhpValues['action']['sources'] = [
            [
                'action' => $action->getId(),
                'name' => 'Foo',
                'link' => 'http://foo.test',
            ],
            [
                'action' => $action->getId(),
                'name' => 'Bar',
                'link' => 'http://bar.test',
            ],
        ];

        $client->request($form->getMethod(), $form->getUri(), $formPhpValues);
        $this->assertRedirectsToRoute($client->getResponse(), 'admin_action_edit');

        $em->clear('App:PromiseAction');
        /** @var PromiseAction $action */
        $action = $em->getRepository('App:PromiseAction')->find($action->getId());

        $this->assertNotNull($action);
        $this->assertCount(2, $action->getSources());

        self::cleanup($em);
    }

    public function testEditActionSubmitSourcesRemoved()
    {
        $client = self::createAdminClient();

        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $source = $this->makePromiseActionSource($em);
        $action = $source->getAction();

        $this->assertCount(1, $action->getSources());

        $form = $client
            ->request('GET', "/${locale}/admin/actions/{$action->getId()}")
            ->filter('form')->form();

        $formPhpValues = $form->getPhpValues();
        unset($formPhpValues['action']['sources']);

        $client->request($form->getMethod(), $form->getUri(), $formPhpValues);
        $this->assertRedirectsToRoute($client->getResponse(), 'admin_action_edit');

        $em->clear('App:PromiseAction');
        $action = $em->getRepository('App:PromiseAction')->find($action->getId());

        $this->assertNotNull($action);
        $this->assertCount(0, $action->getSources());

        self::cleanup($em);
    }

    public function testEditActionSubmitUsedPowersAdded()
    {
        $client = self::createAdminClient();

        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $action = $this->makePromiseAction($em);
        $this->assertCount(0, $action->getUsedPowers());

        {
            $power1 = $this->makePower($em);
            $power2 = $this->makePower($em);

            $action->getMandate()->getInstitutionTitle()->getTitle()->setPowers(new ArrayCollection([$power1, $power2]));
            $em->flush();
        }

        $form = $client
            ->request('GET', "/${locale}/admin/actions/{$action->getId()}")
            ->filter('form')->form();

        $formPhpValues = $form->getPhpValues();
        $formPhpValues['action']['usedPowers'] = [$power1->getId(), $power2->getId()];

        $client->request($form->getMethod(), $form->getUri(), $formPhpValues);
        $this->assertRedirectsToRoute($client->getResponse(), 'admin_action_edit');

        $em->clear('App:PromiseAction');
        /** @var PromiseAction $action */
        $action = $em->getRepository('App:PromiseAction')->find($action->getId());

        $this->assertNotNull($action);
        $this->assertCount(2, $action->getUsedPowers());

        self::cleanup($em);
    }

    public function testEditActionSubmitUsedPowersRemoved()
    {
        $client = self::createAdminClient();

        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $action = $this->makePromiseAction($em);
        $this->assertCount(0, $action->getUsedPowers());

        {
            $usedPowers = new ArrayCollection([$this->makePower($em)]);
            $action->getMandate()->getInstitutionTitle()->getTitle()->setPowers($usedPowers);
            $action->setUsedPowers($usedPowers);
            $em->flush();
        }

        $this->assertCount(1, $action->getUsedPowers());

        $form = $client
            ->request('GET', "/${locale}/admin/actions/{$action->getId()}")
            ->filter('form')->form();

        $formPhpValues = $form->getPhpValues();
        unset($formPhpValues['action']['usedPowers']);

        $client->request($form->getMethod(), $form->getUri(), $formPhpValues);
        $this->assertRedirectsToRoute($client->getResponse(), 'admin_action_edit');

        $em->clear('App:PromiseAction');
        $action = $em->getRepository('App:PromiseAction')->find($action->getId());

        $this->assertNotNull($action);
        $this->assertCount(0, $action->getUsedPowers());

        self::cleanup($em);
    }

    //endregion

    //region Delete

    public function testDeleteActionAccess()
    {
        $client = static::createClient();
        $client->insulate();

        /** @var ObjectManager $manager */
        $manager = $client->getContainer()->get('doctrine')->getManager();
        $action = $this->makePromiseAction($manager);

        $this->assertOnlyAdminCanAccess("/admin/actions/{$action->getId()}/d", $client);

        $manager->remove($action);
        $manager->flush();

        self::cleanup($manager);
    }

    public function testDeleteActionSubmit()
    {
        $client = self::createAdminClient();

        /** @var ObjectManager $manager */
        $manager = $client->getContainer()->get('doctrine')->getManager();
        $locale = self::getLocale($client);

        $action = $this->makePromiseAction($manager);

        $form = $client
            ->request('GET', "/${locale}/admin/actions/{$action->getId()}/d")
            ->filter('form')->form();
        $client->submit($form);
        $this->assertRedirectsToRoute($client->getResponse(), 'admin_promises');

        $manager->clear('App:PromiseAction');

        /** @var Status $action */
        $action = $manager->getRepository('App:PromiseAction')->find($action->getId());

        $this->assertNull($action);

        $manager = null;
        static::$kernel->shutdown();
    }

    //endregion
}
<?php

namespace App\Tests\Controller;

use App\Consts;
use App\Entity\Promise;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;
use Doctrine\Persistence\ObjectManager;

class AdminPromisesControllerTest extends WebTestCase
{
    use TestCaseTrait;

    //region Index

    public function testIndexAction()
    {
        $this->assertOnlyAdminCanAccess('/admin/promises');
    }

    //endregion

    //region Add

    public function testAddActionAccess()
    {
        $this->assertOnlyAdminCanAccess('/admin/promises/add');
    }

    public function testAddActionSubmitInvalidData()
    {
        $client = self::createAdminClient();
        $locale = self::getLocale($client);

        $form = $client
            ->request('GET', "/${locale}/admin/promises/add")
            ->filter('form')->form();
        $client->submit($form, []);

        $this->assertHasFormErrors($client->getResponse());
    }

    public function testAddActionSubmitValidDataWithoutCategories()
    {
        $client = self::createAdminClient();
        $locale = self::getLocale($client);
        $em = self::getDoctrine($client);
        $random = self::randomNumber();

        $formData = [
            'promise[name]' => "Test ${random}",
            'promise[slug]' => "test-${random}",
            'promise[description]' => "Testing ${random}",
            'promise[madeTime]' => (new \DateTime())->format(Consts::DATE_FORMAT_PHP),
            'promise[status]' => self::makeStatus($em)->getId(),
            'promise[election]' => self::makeElection($em)->getId(),
            'promise[politician]' => self::makePolitician($em)->getId(),
            'promise[categories]' => [],
            'promise[published]' => true,
        ];

        $form = $client
            ->request('GET', "/${locale}/admin/promises/add")
            ->filter('form')->form();
        $client->submit($form, $formData);

        $route = $this->assertRedirectsToRoute($client->getResponse(), 'admin_promise_edit');

        /** @var Promise $promise */
        $promise = $em->getRepository('App:Promise')->find($route['id']);

        $this->assertNotNull($promise);
        $this->assertEquals($formData['promise[status]'], $promise->getStatus()->getId());
        $this->assertCount(0, $promise->getCategories());
    }

    public function testAddActionSubmitValidDataWithCategories()
    {
        $client = self::createAdminClient();
        $locale = self::getLocale($client);
        $em = self::getDoctrine($client);
        $random = self::randomNumber();

        $form = $client
            ->request('GET', "/${locale}/admin/promises/add")
            ->filter('form')->form();

        $formPhpValues = $form->getPhpValues();
        $formPhpValues['promise'] = array_merge($formPhpValues['promise'], [
            'name' => "Test ${random}",
            'slug' => "test-${random}",
            'description' => "Testing ${random}",
            'madeTime' => (new \DateTime())->format(Consts::DATE_FORMAT_PHP),
            'status' => self::makeStatus($em)->getId(),
            'election' => self::makeElection($em)->getId(),
            'politician' => self::makePolitician($em)->getId(),
            'published' => true,
            'hasPrerogatives' => true,
            'categories' => [self::makeCategory($em)->getId()],
        ]);

        $client->request($form->getMethod(), $form->getUri(), $formPhpValues);

        $route = $this->assertRedirectsToRoute($client->getResponse(), 'admin_promise_edit');

        /** @var Promise $promise */
        $promise = $em->getRepository('App:Promise')->find($route['id']);

        $this->assertNotNull($promise);
        $this->assertCount(1, $promise->getCategories());
        $this->assertTrue($promise->getHasPrerogatives());
        $this->assertTrue($promise->getPublished());
    }

    public function testAddActionSubmitValidDataWithoutStatus()
    {
        $client = self::createAdminClient();
        $locale = self::getLocale($client);
        $em = self::getDoctrine($client);
        $random = self::randomNumber();

        $formData = [
            'promise[name]' => "Test ${random}",
            'promise[slug]' => "test-${random}",
            'promise[description]' => "Testing ${random}",
            'promise[madeTime]' => (new \DateTime())->format(Consts::DATE_FORMAT_PHP),
            'promise[status]' => '',
            'promise[election]' => self::makeElection($em)->getId(),
            'promise[politician]' => self::makePolitician($em)->getId(),
            'promise[categories]' => [],
            'promise[published]' => false,
            'promise[hasPrerogatives]' => false,
        ];

        $form = $client
            ->request('GET', "/${locale}/admin/promises/add")
            ->filter('form')->form();
        $client->submit($form, $formData);

        $route = $this->assertRedirectsToRoute($client->getResponse(), 'admin_promise_edit');

        /** @var Promise $promise */
        $promise = $em->getRepository('App:Promise')->find($route['id']);

        $this->assertNotNull($promise);
        $this->assertNull($promise->getStatus());
        $this->assertFalse($promise->getHasPrerogatives());
        $this->assertFalse($promise->getPublished());
    }

    //endregion

    //region Edit

    public function testEditActionAccess()
    {
        static::ensureKernelShutdown();
        $client = static::createClient();
        $client->insulate();

        $em = self::getDoctrine($client);
        $promise = self::makePromise($em);

        $this->assertOnlyAdminCanAccess("/admin/promises/{$promise->getId()}", $client);
    }

    public function testEditActionSubmitInvalidData()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $promise = self::makePromise($em);

        $form = $client
            ->request('GET', "/${locale}/admin/promises/{$promise->getId()}")
            ->filter('form')->form();
        $client->submit($form, ['promise[name]' => '',]);

        $this->assertHasFormErrors($client->getResponse());

        self::cleanup($em);
    }

    public function testEditActionSubmitAddCategories()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $promise = self::makePromise($em);
        $this->assertCount(0, $promise->getCategories());

        $form = $client
            ->request('GET', "/${locale}/admin/promises/{$promise->getId()}")
            ->filter('form')->form();

        $formPhpValues = $form->getPhpValues();
        $formPhpValues['promise']['categories'] = [self::makeCategory($em)->getId()];

        $client->request($form->getMethod(), $form->getUri(), $formPhpValues);

        $this->assertRedirectsToRoute($client->getResponse(), 'admin_promise_edit');

        $em->clear();
        /** @var Promise $promise */
        $promise = $em->getRepository('App:Promise')->find($promise->getId());

        $this->assertNotNull($promise);

        $this->assertCount(1, $promise->getCategories());

        self::cleanup($em);
    }

    public function testEditActionSubmitRemoveCategories()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $promise = self::makePromise($em);
        $promise->setCategories(new ArrayCollection([self::makeCategory($em)]));
        $em->flush();

        $form = $client
            ->request('GET', "/${locale}/admin/promises/{$promise->getId()}")
            ->filter('form')->form();

        $formPhpValues = $form->getPhpValues();
        $this->assertCount(1, $formPhpValues['promise']['categories']);
        $formPhpValues['promise']['categories'] = [];

        $client->request($form->getMethod(), $form->getUri(), $formPhpValues);

        $this->assertRedirectsToRoute($client->getResponse(), 'admin_promise_edit');

        $em->clear();
        /** @var Promise $promise */
        $promise = $em->getRepository('App:Promise')->find($promise->getId());

        $this->assertNotNull($promise);

        $this->assertCount(0, $promise->getCategories());

        self::cleanup($em);
    }

    public function testEditActionSubmitEmptyStatus()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $promise = self::makePromise($em);
        $promise->setStatus(self::makeStatus($em));
        $em->flush();

        $form = $client
            ->request('GET', "/${locale}/admin/promises/{$promise->getId()}")
            ->filter('form')->form();

        $formPhpValues = $form->getPhpValues();
        $this->assertNotEmpty($formPhpValues['promise']['status']);
        $formPhpValues['promise']['status'] = '';

        $client->request($form->getMethod(), $form->getUri(), $formPhpValues);

        $this->assertRedirectsToRoute($client->getResponse(), 'admin_promise_edit');

        $em->clear();
        /** @var Promise $promise */
        $promise = $em->getRepository('App:Promise')->find($promise->getId());

        $this->assertNotNull($promise);

        $this->assertNull($promise->getStatus());

        self::cleanup($em);
    }

    public function testEditActionSubmitAddSources()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);
        $random = self::randomNumber();

        $promise = self::makePromise($em);
        $this->assertCount(0, $promise->getSources());

        $form = $client
            ->request('GET', "/${locale}/admin/promises/{$promise->getId()}")
            ->filter('form')->form();

        $formPhpValues = $form->getPhpValues();
        $formPhpValues['promise']['sources'] = [
            [
                'promise' => $promise->getId(),
                'name' => "Test ${random}",
                'link' => "http://source.link/${random}",
            ]
        ];

        $client->request($form->getMethod(), $form->getUri(), $formPhpValues);

        $this->assertRedirectsToRoute($client->getResponse(), 'admin_promise_edit');

        $em->clear();
        /** @var Promise $promise */
        $promise = $em->getRepository('App:Promise')->find($promise->getId());

        $this->assertNotNull($promise);

        $this->assertCount(1, $promise->getSources());

        self::cleanup($em);
    }

    public function testEditActionSubmitRemoveSources()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $source = self::makePromiseSource($em);
        $promise = $source->getPromise();

        $form = $client
            ->request('GET', "/${locale}/admin/promises/{$promise->getId()}")
            ->filter('form')->form();

        $formPhpValues = $form->getPhpValues();
        $this->assertCount(1, $formPhpValues['promise']['sources']);
        $formPhpValues['promise']['sources'] = [];

        $client->request($form->getMethod(), $form->getUri(), $formPhpValues);

        $this->assertRedirectsToRoute($client->getResponse(), 'admin_promise_edit');

        $em->clear();
        /** @var Promise $promise */
        $promise = $em->getRepository('App:Promise')->find($promise->getId());

        $this->assertNotNull($promise);

        $this->assertCount(0, $promise->getSources());

        self::cleanup($em);
    }

    //endregion

    //region Delete

    public function testDeleteActionAccess()
    {
        static::ensureKernelShutdown();
        $client = static::createClient();
        $client->insulate();

        $em = self::getDoctrine($client);
        $promise = $this->makePromise($em);

        $this->assertOnlyAdminCanAccess("/admin/promises/{$promise->getId()}/d", $client);

        self::cleanup($em);
    }

    public function testDeleteActionSubmit()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $promise = $this->makePromise($em);

        $form = $client
            ->request('GET', "/${locale}/admin/promises/{$promise->getId()}/d")
            ->filter('form')->form();
        $client->submit($form);
        $this->assertRedirectsToRoute($client->getResponse(), 'admin_promises');

        $em->clear();
        /** @var Promise $promise */
        $promise = $em->getRepository('App:Promise')->find($promise->getId());

        $this->assertNull($promise);

        self::cleanup($em);
    }

    //endregion
}
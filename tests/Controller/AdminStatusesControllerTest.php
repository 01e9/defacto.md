<?php

namespace App\Tests\Controller;

use App\Entity\Status;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;
use Doctrine\Persistence\ObjectManager;

class AdminStatusesControllerTest extends WebTestCase
{
    use TestCaseTrait;

    //region Add

    public function testAddActionAccess()
    {
        $this->assertOnlyAdminCanAccess('/admin/statuses/add');
    }

    public function testAddActionSubmitInvalidData()
    {
        $client = self::createAdminClient();
        $locale = self::getLocale($client);

        $form = $client
            ->request('GET', "/${locale}/admin/statuses/add")
            ->filter('form')->form();
        $client->submit($form, []);

        $this->assertHasFormErrors($client->getResponse());
    }

    public function testAddActionSubmitValidData()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);
        $random = self::randomNumber();

        $formData = [
            'status[name]' => "Test ${random}",
            'status[namePlural]' => "Tests ${random}",
            'status[slug]' => "test-${random}",
            'status[color]' => 'blue',
            'status[effect]' => $random,
        ];

        $form = $client
            ->request('GET', "/${locale}/admin/statuses/add")
            ->filter('form')->form();
        $client->submit($form, $formData);
        $route = $this->assertRedirectsToRoute($client->getResponse(), 'admin_status_edit');

        /** @var Status $status */
        $status = $em->getRepository('App:Status')->find($route['id']);

        $this->assertNotNull($status);
        $this->assertEquals($formData['status[name]'], $status->getName());
        $this->assertEquals($formData['status[color]'], $status->getColor());

        self::cleanup($em);
    }

    //endregion

    //region Edit

    public function testEditActionAccess()
    {
        static::ensureKernelShutdown();
        $client = static::createClient();
        $client->insulate();

        $em = self::getDoctrine($client);
        $status = self::makeStatus($em);

        $this->assertOnlyAdminCanAccess("/admin/statuses/{$status->getId()}", $client);
    }

    public function testEditActionSubmitInvalidData()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $status = self::makeStatus($em);
        $form = $client
            ->request('GET', "/${locale}/admin/statuses/{$status->getId()}")
            ->filter('form')->form();
        $client->submit($form, ['status[name]' => '?',]);

        $this->assertHasFormErrors($client->getResponse());

        self::cleanup($em);
    }

    public function testEditActionSubmitValidData()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);
        $random = self::randomNumber();

        $formData = [
            'status[name]' => "Test ${random}",
            'status[namePlural]' => "Tests ${random}",
            'status[slug]' => "test-${random}",
            'status[color]' => 'blue',
            'status[effect]' => $random,
        ];

        $status = self::makeStatus($em);
        $form = $client
            ->request('GET', "/${locale}/admin/statuses/{$status->getId()}")
            ->filter('form')->form();
        $client->submit($form, $formData);
        $this->assertRedirectsToRoute($client->getResponse(), 'admin_status_edit');

        $em->clear();
        /** @var Status $status */
        $status = $em->getRepository('App:Status')->find($status->getId());

        $this->assertNotNull($status);

        $this->assertEquals($formData['status[name]'], $status->getName());
        $this->assertEquals($formData['status[color]'], $status->getColor());

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
        $status = $this->makeStatus($em);

        $this->assertOnlyAdminCanAccess("/admin/statuses/{$status->getId()}/d", $client);

        self::cleanup($em);
    }

    public function testDeleteActionSubmit()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $status = $this->makeStatus($em);

        $form = $client
            ->request('GET', "/${locale}/admin/statuses/{$status->getId()}/d")
            ->filter('form')->form();
        $client->submit($form);
        $this->assertRedirectsToRoute($client->getResponse(), 'admin_settings');

        $em->clear();
        /** @var Status $status */
        $status = $em->getRepository('App:Status')->find($status->getId());

        $this->assertNull($status);

        self::cleanup($em);
    }

    //endregion
}
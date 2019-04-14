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

    //region List

    public function testIndexAction()
    {
        $this->assertOnlyAdminCanAccess('/admin/parties');
    }

    //endregion

    //region Add

    public function testAddActionAccess()
    {
        $this->assertOnlyAdminCanAccess('/admin/parties/add');
    }

    public function testAddActionSubmitInvalidData()
    {
        $client = self::createAdminClient();
        $locale = self::getLocale($client);

        $form = $client
            ->request('GET', "/${locale}/admin/parties/add")
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
            'party[name]' => "Test ${random}",
            'party[slug]' => "test-${random}",
        ];

        $form = $client
            ->request('GET', "/${locale}/admin/parties/add")
            ->filter('form')->form();
        $client->submit($form, $formData);

        $route = $this->assertRedirectsToRoute($client->getResponse(), 'admin_party_edit');

        $em->clear('App:Party');
        /** @var Party $party */
        $party = $em->getRepository('App:Party')->find($route['id']);

        $this->assertNotNull($party);
        $this->assertEquals($formData['party[name]'], $party->getName());

        self::cleanup($em);
    }

    public function testAddActionSubmitUploadLogo()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);
        $random = self::randomNumber();

        $formData = [
            'party[name]' => "Test ${random}",
            'party[slug]' => "test-${random}",
        ];

        $logo = new UploadedFile(self::getTestsRootDir() . '/files/test.jpg', 'test.jpg');

        $form = $client
            ->request('GET', "/${locale}/admin/parties/add")
            ->filter('form')->form();
        $client->insulate(false);
        $client->submit($form, array_merge($formData, [
            'party[logo]' => $logo,
        ]));
        $route = $this->assertRedirectsToRoute($client->getResponse(), 'admin_party_edit');

        $client = self::createAdminClient();
        $em = $this->getDoctrine($client);

        /** @var Party $party */
        $party = $em->getRepository('App:Party')->find($route['id']);
        $em->refresh($party); // fix lifecycle callbacks

        $this->assertNotNull($party);
        $this->assertEquals($logo->getMimeType(), $party->getLogo()->getMimeType());

        self::cleanup($em);
    }

    //endregion

    //region Edit

    public function testEditActionAccess()
    {
        $client = static::createClient();
        $client->insulate();
        $em = self::getDoctrine($client);
        $party = $this->makeParty($em);

        $this->assertOnlyAdminCanAccess("/admin/parties/{$party->getId()}", $client);
    }

    public function testEditActionSubmitInvalidData()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $party = $this->makeParty($em);
        $form = $client
            ->request('GET', "/${locale}/admin/parties/{$party->getId()}")
            ->filter('form')->form();
        $client->submit($form, [
            'party[name]' => '?',
        ]);

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
            'party[name]' => "Test ${random}",
            'party[slug]' => "test-${random}",
        ];

        $party = $this->makeParty($em);
        $form = $client
            ->request('GET', "/${locale}/admin/parties/{$party->getId()}")
            ->filter('form')->form();
        $client->submit($form, $formData);

        $this->assertRedirectsToRoute($client->getResponse(), 'admin_party_edit');

        self::cleanup($em);
    }

    public function testEditActionSubmitUploadLogo()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);
        $random = self::randomNumber();

        $formData = [
            'party[name]' => "Test ${random}",
            'party[slug]' => "test-${random}",
        ];

        $party = $this->makeParty($em);
        $logo = new UploadedFile(self::getTestsRootDir() . '/files/test.gif', 'test.gif');
        $form = $client
            ->request('GET', "/${locale}/admin/parties/{$party->getId()}")
            ->filter('form')->form();
        $client->insulate(false);
        $client->submit($form, array_merge($formData, ['party[logo]' => $logo,]));
        $this->assertRedirectsToRoute($client->getResponse(), 'admin_party_edit');

        $client = self::createAdminClient();
        $em = $this->getDoctrine($client);

        $party = $em->getRepository('App:Party')->find($party->getId());

        $this->assertNotNull($party);
        $em->refresh($party); // fix lifecycle callbacks

        $this->assertEquals($logo->getMimeType(), $party->getLogo()->getMimeType());

        self::cleanup($em);
    }

    //endregion

    //region Delete

    public function testDeleteActionAccess()
    {
        $client = static::createClient();
        $client->insulate();

        $em = self::getDoctrine($client);
        $party = $this->makeParty($em);

        $this->assertOnlyAdminCanAccess("/admin/parties/{$party->getId()}/d", $client);

        self::cleanup($em);
    }

    public function testDeleteActionSubmit()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $party = $this->makeParty($em);

        $form = $client
            ->request('GET', "/${locale}/admin/parties/{$party->getId()}/d")
            ->filter('form')->form();
        $client->submit($form);
        $this->assertRedirectsToRoute($client->getResponse(), 'admin_parties');

        $em->clear('App:Party');
        /** @var Party $party */
        $party = $em->getRepository('App:Party')->find($party->getId());

        $this->assertNull($party);

        self::cleanup($em);
    }

    //endregion
}
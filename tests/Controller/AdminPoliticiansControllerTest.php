<?php

namespace App\Tests\Controller;

use App\Entity\Politician;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AdminPoliticiansControllerTest extends WebTestCase
{
    use TestCaseTrait;

    //region List

    public function testIndexAction()
    {
        $this->assertOnlyAdminCanAccess('/admin/politicians');
    }

    //endregion

    //region Add

    public function testAddActionAccess()
    {
        $this->assertOnlyAdminCanAccess('/admin/politicians/add');
    }

    public function testAddActionSubmitInvalidData()
    {
        $client = self::createAdminClient();
        $locale = self::getLocale($client);

        $form = $client
            ->request('GET', "/${locale}/admin/politicians/add")
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
            'politician[firstName]' => "Foo",
            'politician[lastName]' => "Bar",
            'politician[slug]' => "tes-${random}",
        ];

        $form = $client
            ->request('GET', "/${locale}/admin/politicians/add")
            ->filter('form')->form();
        $client->submit($form, $formData);
        $route = $this->assertRedirectsToRoute($client->getResponse(), 'admin_politician_edit');

        /** @var Politician $politician */
        $politician = $em->getRepository('App:Politician')->find($route['id']);

        $this->assertNotNull($politician);
        $this->assertEquals($formData['politician[slug]'], $politician->getSlug());
    }

    public function testAddActionSubmitUploadPhoto()
    {
        $client = self::createAdminClient();
        $locale = self::getLocale($client);
        $random = self::randomNumber();

        $formData = [
            'politician[firstName]' => "Foo",
            'politician[lastName]' => "Bar",
            'politician[slug]' => "tes-${random}",
        ];

        $photo = new UploadedFile(self::getTestsRootDir() . '/files/test.jpg', 'test.jpg');

        $form = $client
            ->request('GET', "/${locale}/admin/politicians/add")
            ->filter('form')->form();
        $client->insulate(false);
        $client->submit($form, array_merge($formData, ['politician[photoUpload]' => $photo,]));
        $route = $this->assertRedirectsToRoute($client->getResponse(), 'admin_politician_edit');

        $client = self::createAdminClient();
        $em = self::getDoctrine($client);

        /** @var Politician $politician */
        $politician = $em->getRepository('App:Politician')->find($route['id']);
        $em->refresh($politician); // fix lifecycle callbacks

        $this->assertNotNull($politician);
        $this->assertEquals($photo->guessExtension(), explode(".", $politician->getPhoto())[1]);

        self::cleanup($em);
    }

    //endregion

    //region Edit

    public function testEditActionAccess()
    {
        $client = static::createClient();
        $client->insulate();

        $em = self::getDoctrine($client);
        $politician = self::makePolitician($em);

        $this->assertOnlyAdminCanAccess("/admin/politicians/{$politician->getId()}", $client);
    }

    public function testEditActionSubmitInvalidData()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $politician = $this->makePolitician($em);

        $form = $client
            ->request('GET', "/${locale}/admin/politicians/{$politician->getId()}")
            ->filter('form')->form();
        $client->submit($form, ['politician[firstName]' => '?',]);

        $this->assertHasFormErrors($client->getResponse());
    }

    public function testEditActionSubmitValidData()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);
        $random = self::randomNumber();

        $formData = [
            'politician[firstName]' => "Foo",
            'politician[lastName]' => "Bar",
            'politician[slug]' => "tes-${random}",
        ];

        $politician = $this->makePolitician($em);

        $form = $client
            ->request('GET', "/${locale}/admin/politicians/{$politician->getId()}")
            ->filter('form')->form();
        $client->submit($form, $formData);
        $this->assertRedirectsToRoute($client->getResponse(), 'admin_politician_edit');

        $em->clear('App:Politician');
        $politician = $em->getRepository('App:Politician')->find($politician->getId());

        $this->assertNotNull($politician);
        $this->assertEquals($formData['politician[slug]'], $politician->getSlug());
    }

    public function testEditActionSubmitUploadPhoto()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);
        $random = self::randomNumber();

        $formData = [
            'politician[firstName]' => "Foo",
            'politician[lastName]' => "Bar",
            'politician[slug]' => "tes-${random}",
        ];
        $photo = new UploadedFile(self::getTestsRootDir() . '/files/test.gif', 'test.gif');

        $politician = $this->makePolitician($em);

        $form = $client
            ->request('GET', "/${locale}/admin/politicians/{$politician->getId()}")
            ->filter('form')->form();
        $client->insulate(false);
        $client->submit($form, array_merge($formData, ['politician[photoUpload]' => $photo,]));
        $this->assertRedirectsToRoute($client->getResponse(), 'admin_politician_edit');

        $client = self::createAdminClient();
        $em = self::getDoctrine($client);

        $em->clear('App:Politician');
        $politician = $em->getRepository('App:Politician')->find($politician->getId());

        $this->assertNotNull($politician);
        $em->refresh($politician); // fix lifecycle callbacks

        $this->assertEquals($photo->guessExtension(), explode(".", $politician->getPhoto())[1]);

        self::cleanup($em);
    }

    //endregion

    //region Delete

    public function testDeleteActionAccess()
    {
        $client = static::createClient();
        $client->insulate();

        $em = self::getDoctrine($client);
        $politician = $this->makePolitician($em);

        $this->assertOnlyAdminCanAccess("/admin/politicians/{$politician->getId()}/d", $client);

        self::cleanup($em);
    }

    public function testDeleteActionSubmit()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $politician = $this->makePolitician($em);

        $form = $client
            ->request('GET', "/${locale}/admin/politicians/{$politician->getId()}/d")
            ->filter('form')->form();
        $client->submit($form);
        $this->assertRedirectsToRoute($client->getResponse(), 'admin_politicians');

        $em->clear('App:Politician');
        /** @var Politician $politician */
        $politician = $em->getRepository('App:Politician')->find($politician->getId());

        $this->assertNull($politician);

        self::cleanup($em);
    }

    //endregion
}
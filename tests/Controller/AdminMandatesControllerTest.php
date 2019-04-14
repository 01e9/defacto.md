<?php

namespace App\Tests\Controller;

use App\Consts;
use App\Entity\Mandate;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;

class AdminMandatesControllerTest extends WebTestCase
{
    use TestCaseTrait;

    //region List

    public function testIndexActionAccess()
    {
        $this->assertOnlyAdminCanAccess('/admin/mandates');
    }

    //endregion

    //region Add

    public function testAddActionAccess()
    {
        $this->assertOnlyAdminCanAccess('/admin/mandates/add');
    }

    public function testAddActionSubmitEmptyForm()
    {
        $client = self::createAdminClient();
        $locale = self::getLocale($client);

        $form = $client
            ->request('GET', "/${locale}/admin/mandates/add")
            ->filter('form')->form();
        $client->submit($form, []);

        $this->assertHasFormErrors($client->getResponse());
    }

    public function testAddActionSubmitValidData()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $formData = [
            'mandate[votesCount]' => 1000000,
            'mandate[votesPercent]' => 51,
            'mandate[beginDate]' => (new \DateTime('-2 years'))->format(Consts::DATE_FORMAT_PHP),
            'mandate[endDate]' => (new \DateTime('+2 years'))->format(Consts::DATE_FORMAT_PHP),
            'mandate[election]' => $this->makeElection($em)->getId(),
            'mandate[politician]' => $this->makePolitician($em)->getId(),
            'mandate[institutionTitle]' => $this->makeInstitutionTitle($em)->getId(),
        ];

        $form = $client
            ->request('GET', "/${locale}/admin/mandates/add")
            ->filter('form')->form();
        $client->submit($form, $formData);
        $route = $this->assertRedirectsToRoute($client->getResponse(), 'admin_mandate_edit');

        $em->clear('App:Mandate');
        /** @var Mandate $mandate */
        $mandate = $em->getRepository('App:Mandate')->find($route['id']);

        $this->assertNotNull($mandate);
        $this->assertEquals($formData['mandate[politician]'], $mandate->getPolitician()->getId());

        self::cleanup($em);
    }

    //endregion

    //region Edit

    public function testEditActionAccess()
    {
        $client = static::createClient();
        $client->insulate();
        $em = self::getDoctrine($client);

        $mandate = $em->getRepository('App:Mandate')->findOneBy([]);

        $this->assertOnlyAdminCanAccess("/admin/mandates/{$mandate->getId()}", $client);
    }

    public function testEditActionSubmitInvalidData()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $mandate = self::makeMandate($em);
        $form = $client
            ->request('GET', "/${locale}/admin/mandates/{$mandate->getId()}")
            ->filter('form')->form();
        $client->submit($form, ['mandate[votesCount]' => '?']);

        $this->assertHasFormErrors($client->getResponse());
    }

    public function testEditActionSubmitValidData()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $formData = [
            'mandate[votesCount]' => self::randomNumber(),
            'mandate[votesPercent]' => 51,
            'mandate[beginDate]' => (new \DateTime('-2 years'))->format(Consts::DATE_FORMAT_PHP),
            'mandate[endDate]' => (new \DateTime('+2 years'))->format(Consts::DATE_FORMAT_PHP),
            'mandate[election]' => self::makeElection($em)->getId(),
            'mandate[politician]' => self::makePolitician($em)->getId(),
            'mandate[institutionTitle]' => self::makeInstitutionTitle($em)->getId(),
        ];

        $mandate = self::makeMandate($em);
        $form = $client
            ->request('GET', "/${locale}/admin/mandates/{$mandate->getId()}")
            ->filter('form')->form();
        $client->submit($form, $formData);
        $this->assertRedirectsToRoute($client->getResponse(), 'admin_mandate_edit');

        $em->clear('App:Mandate');
        /** @var Mandate $mandate */
        $mandate = $em->getRepository('App:Mandate')->find($mandate->getId());

        $this->assertNotNull($mandate);
        $this->assertEquals($formData['mandate[votesCount]'], $mandate->getVotesCount());

        self::cleanup($em);
    }

    //endregion

    //region Delete

    public function testDeleteActionAccess()
    {
        $client = static::createClient();
        $client->insulate();
        $em = self::getDoctrine($client);
        $mandate = $this->makeMandate($em);

        $this->assertOnlyAdminCanAccess("/admin/mandates/{$mandate->getId()}/d", $client);

        self::cleanup($em);
    }

    public function testDeleteActionSubmit()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $mandate = $this->makeMandate($em);

        $form = $client
            ->request('GET', "/${locale}/admin/mandates/{$mandate->getId()}/d")
            ->filter('form')->form();
        $client->submit($form);
        $this->assertRedirectsToRoute($client->getResponse(), 'admin_mandates');

        $em->clear('App:Mandate');
        /** @var Mandate $mandate */
        $mandate = $em->getRepository('App:Mandate')->find($mandate->getId());

        $this->assertNull($mandate);

        self::cleanup($em);
    }

    //endregion
}
<?php

namespace App\Tests\Controller;

use App\Entity\Problem;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;
use Doctrine\Persistence\ObjectManager;

class AdminProblemsControllerTest extends WebTestCase
{
    use TestCaseTrait;

    //region Index

    public function testIndexActionAccess()
    {
        $this->assertOnlyAdminCanAccess('/admin/problems');
    }

    //endregion

    //region Add

    public function testAddActionAccess()
    {
        $this->assertOnlyAdminCanAccess('/admin/problems/add');
    }

    public function testAddActionSubmitInvalidData()
    {
        $client = self::createAdminClient();
        $locale = self::getLocale($client);

        $form = $client
            ->request('GET', "/${locale}/admin/problems/add")
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
            'problem[name]' => "Test ${random}",
            'problem[slug]' => "test-${random}",
        ];

        $form = $client
            ->request('GET', "/${locale}/admin/problems/add")
            ->filter('form')->form();
        $client->submit($form, $formData);
        $route = $this->assertRedirectsToRoute($client->getResponse(), 'admin_problem_edit');

        $em->clear();
        /** @var Problem $problem */
        $problem = $em->getRepository('App:Problem')->find($route['id']);

        $this->assertNotNull($problem);
        $this->assertEquals($formData['problem[name]'], $problem->getName());
        $this->assertEquals($formData['problem[slug]'], $problem->getSlug());

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
        $problem = $this->makeProblem($em);

        $this->assertOnlyAdminCanAccess("/admin/problems/{$problem->getId()}", $client);
    }

    public function testEditActionSubmitInvalidData()
    {
        $client = static::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $problem = $this->makeProblem($em);
        $form = $client
            ->request('GET', "/${locale}/admin/problems/{$problem->getId()}")
            ->filter('form')->form();
        $client->submit($form, ['problem[name]' => '?',]);

        $this->assertHasFormErrors($client->getResponse());

        self::cleanup($em);
    }

    public function testEditActionSubmitValidData()
    {
        $client = static::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);
        $random = self::randomNumber();

        $formData = [
            'problem[name]' => "Test ${random}",
            'problem[slug]' => "test-${random}",
        ];

        $problem = $this->makeProblem($em);
        $form = $client
            ->request('GET', "/${locale}/admin/problems/{$problem->getId()}")
            ->filter('form')->form();
        $client->submit($form, $formData);
        $this->assertRedirectsToRoute($client->getResponse(), 'admin_problem_edit');

        $em->clear();
        /** @var Problem $problem */
        $problem = $em->getRepository('App:Problem')->find($problem->getId());

        $this->assertNotNull($problem);
        $this->assertEquals($formData['problem[name]'], $problem->getName());
        $this->assertEquals($formData['problem[slug]'], $problem->getSlug());

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
        $problem = $this->makeProblem($em);

        $this->assertOnlyAdminCanAccess("/admin/problems/{$problem->getId()}/d", $client);

        self::cleanup($em);
    }

    public function testDeleteActionSubmit()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $problem = $this->makeProblem($em);

        $form = $client
            ->request('GET', "/${locale}/admin/problems/{$problem->getId()}/d")
            ->filter('form')->form();
        $client->submit($form);
        $this->assertRedirectsToRoute($client->getResponse(), 'admin_problems');

        $em->clear();
        /** @var Problem $problem */
        $problem = $em->getRepository('App:Problem')->find($problem->getId());

        $this->assertNull($problem);

        self::cleanup($em);
    }

    //endregion
}
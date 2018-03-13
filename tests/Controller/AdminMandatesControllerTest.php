<?php

namespace App\Tests\Controller;

use App\Entity\Mandate;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;
use Doctrine\Common\Persistence\ObjectManager;

class AdminMandatesControllerTest extends WebTestCase
{
    use TestCaseTrait;

    public function testAddActionAccess()
    {
        $client = static::createClient();
        $client->insulate();
        $this->assertTrue(self::onlyAdminCanAccess('/admin/mandates/add', $client));
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
            'mandate[votesCount]' => 1000000,
            'mandate[votesPercent]' => 51,
            'mandate[beginDate]' => (new \DateTime('-2 years'))->format('Y-m-d'),
            'mandate[endDate]' => (new \DateTime('+2 years'))->format('Y-m-d'),
            'mandate[politician]' => $em->getRepository('App:Politician')->findOneBy([])->getId(),
            'mandate[institutionTitle]' => $em->getRepository('App:InstitutionTitle')->findOneBy([])->getId(),
        ];

        foreach (self::getLangs() as $lang) {
            $form = $client
                ->request('GET', '/'. $lang .'/admin/mandates/add')
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
                $this->assertEquals('admin_mandate_edit', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                /** @var Mandate $mandate */
                $mandate = $em->getRepository('App:Mandate')->find($route['id']);

                $this->assertNotNull($mandate);
                $this->assertEquals($formData['mandate[politician]'], $mandate->getPolitician()->getId());

                $em->remove($mandate);
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
        $mandate = $manager->getRepository('App:Mandate')->findOneBy([]);

        $this->assertTrue(self::onlyAdminCanAccess('/admin/mandates/'. $mandate->getId(), $client));
    }

    public function testEditActionSubmit()
    {
        $client = static::createClient();
        $client->insulate();
        $client->followRedirects(false);
        self::logInClientAsRole($client, 'ROLE_ADMIN');

        $em = $client->getContainer()->get('doctrine.orm.default_entity_manager');
        $router = $client->getContainer()->get('router');

        $mandate = new Mandate();
        $mandate
            ->setVotesCount(1000000)
            ->setVotesPercent(51)
            ->setBeginDate(new \DateTime('-2 years'))
            ->setEndDate(new \DateTime('+2 years'))
            ->setPolitician($em->getRepository('App:Politician')->findOneBy([]))
            ->setInstitutionTitle($em->getRepository('App:InstitutionTitle')->findOneBy([]));
        $em->persist($mandate);
        $em->flush();

        $formData = [
            'mandate[votesCount]' => 1000001,
            'mandate[votesPercent]' => 51,
            'mandate[beginDate]' => (new \DateTime('-2 years'))->format('Y-m-d'),
            'mandate[endDate]' => (new \DateTime('+2 years'))->format('Y-m-d'),
            'mandate[politician]' => $em->getRepository('App:Politician')->findOneBy([])->getId(),
            'mandate[institutionTitle]' => $em->getRepository('App:InstitutionTitle')->findOneBy([])->getId(),
        ];

        foreach (self::getLangs() as $lang) {
            $form = $client
                ->request('GET', '/'. $lang .'/admin/mandates/'. $mandate->getId())
                ->filter('form')->form();

            {
                $client->submit($form, [
                    'mandate[votesCount]' => '?',
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
                $this->assertEquals('admin_politicians', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                $mandate = $em->getRepository('App:Mandate')->find($mandate->getId());

                $this->assertNotNull($mandate);

                $em->refresh($mandate);

                $this->assertEquals($formData['mandate[votesCount]'], $mandate->getVotesCount());
            }
        }

        $em->remove($mandate);
        $em->flush();

        $em->close();
        $em = null;
        static::$kernel->shutdown();
    }
}
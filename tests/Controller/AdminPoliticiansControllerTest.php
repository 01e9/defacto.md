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

    public function testIndexAction()
    {
        $client = static::createClient();
        $client->insulate();
        $this->assertTrue(self::onlyAdminCanAccess('/admin/politicians', $client));
    }

    public function testAddActionAccess()
    {
        $client = static::createClient();
        $client->insulate();
        $this->assertTrue(self::onlyAdminCanAccess('/admin/politicians/add', $client));
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
            'politician[firstName]' => 'Test',
            'politician[lastName]' => 'Test',
            'politician[slug]' => 'test',
        ];

        foreach (self::getLangs() as $lang) {
            $form = $client
                ->request('GET', '/'. $lang .'/admin/politicians/add')
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
                $this->assertEquals('admin_politician_edit', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                $politician = $em->getRepository('App:Politician')->find($route['id']);

                $this->assertNotNull($politician);

                $em->remove($politician);
                $em->flush();
            }

            {
                $photo = new UploadedFile(self::getTestsRootDir() . '/files/test.jpg', 'test.jpg');

                $client->insulate(false);
                $client->submit($form, array_merge($formData, [
                    'politician[photo]' => $photo,
                ]));
                $client->insulate(true);
                $response = $client->getResponse();
                $this->assertEquals(302, $response->getStatusCode());

                $route = $router->match($response->getTargetUrl());
                $this->assertEquals('admin_politician_edit', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                /** @var Politician $politician */
                $politician = $em->getRepository('App:Politician')->find($route['id']);
                $em->refresh($politician); // fix lifecycle callbacks call

                $this->assertNotNull($politician);
                $this->assertEquals($photo->getMimeType(), $politician->getPhoto()->getMimeType());

                $em->remove($politician);
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
        $politician = $manager->getRepository('App:Politician')->findOneBy([]);

        $this->assertTrue(self::onlyAdminCanAccess('/admin/politicians/'. $politician->getId(), $client));
    }

    public function testEditActionSubmit()
    {
        $client = static::createClient();
        $client->insulate();
        $client->followRedirects(false);
        self::logInClientAsRole($client, 'ROLE_ADMIN');

        $em = $client->getContainer()->get('doctrine.orm.default_entity_manager');
        $router = $client->getContainer()->get('router');

        $politician = new Politician();
        $politician
            ->setFirstName('Test')
            ->setLastName('Test')
            ->setSlug('test');
        $em->persist($politician);
        $em->flush();

        $formData = [
            'politician[firstName]' => 'Test',
            'politician[lastName]' => 'Test',
            'politician[slug]' => 'test',
        ];

        foreach (self::getLangs() as $lang) {
            $form = $client
                ->request('GET', '/'. $lang .'/admin/politicians/'. $politician->getId())
                ->filter('form')->form();

            {
                $client->submit($form, [
                    'politician[firstName]' => '?',
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
            }

            {
                $photo = new UploadedFile(self::getTestsRootDir() . '/files/test.gif', 'test.gif');

                $client->insulate(false);
                $client->submit($form, array_merge($formData, [
                    'politician[photo]' => $photo,
                ]));
                $client->insulate(true);
                $response = $client->getResponse();
                $this->assertEquals(302, $response->getStatusCode());

                $route = $router->match($response->getTargetUrl());
                $this->assertEquals('admin_politicians', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                $politician = $em->getRepository('App:Politician')->find($politician->getId());

                $this->assertNotNull($politician);

                $em->refresh($politician);

                $this->assertEquals($photo->getMimeType(), $politician->getPhoto()->getMimeType());
            }
        }

        $em->remove($politician);
        $em->flush();

        $em->close();
        $em = null;
        static::$kernel->shutdown();
    }
}
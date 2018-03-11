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
        $this->assertTrue(self::onlyAdminCanAccess('/admin/politicians', $client));
    }

    public function testAddActionAccess()
    {
        $client = static::createClient();
        $this->assertTrue(self::onlyAdminCanAccess('/admin/politicians/add', $client));
    }

    public function testAddActionSubmit()
    {
        $client = static::createClient();
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
                $em->remove($politician);
                $em->flush();
            }

            {
                $photo = new UploadedFile(self::getTestsRootDir() . '/files/test.jpg', 'test.jpg');

                $client->submit($form, array_merge($formData, [
                    'politician[photo]' => $photo,
                ]));
                $response = $client->getResponse();
                $this->assertEquals(302, $response->getStatusCode());

                $route = $router->match($response->getTargetUrl());
                $this->assertEquals('admin_politician_edit', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                /** @var Politician $politician */
                $politician = $em->getRepository('App:Politician')->find($route['id']);

                $this->assertEquals($photo->getMimeType(), $politician->getPhoto()->getMimeType());

                $em->remove($politician);
                $em->flush();
            }
        }
    }

    public function testEditActionAccess()
    {
        $client = static::createClient();

        /** @var ObjectManager $manager */
        $manager = $client->getContainer()->get('doctrine')->getManager();
        $politician = $manager->getRepository('App:Politician')->findOneBy([]);

        $this->assertTrue(self::onlyAdminCanAccess('/admin/politicians/'. $politician->getId(), $client));
    }

    public function testEditActionSubmit()
    {
        $client = static::createClient();
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

                $client->submit($form, array_merge($formData, [
                    'politician[photo]' => $photo,
                ]));
                $response = $client->getResponse();
                $this->assertEquals(302, $response->getStatusCode());

                $route = $router->match($response->getTargetUrl());
                $this->assertEquals('admin_politicians', $route['_route']);
                $this->assertEquals($lang, $route['_locale']);

                $politician = $em->getRepository('App:Politician')->find($politician->getId());
                $em->refresh($politician);

                $this->assertEquals($photo->getMimeType(), $politician->getPhoto()->getMimeType());
            }
        }

        $em->remove($politician);
        $em->flush();
    }
}
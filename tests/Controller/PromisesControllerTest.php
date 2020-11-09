<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;

class PromisesControllerTest extends WebTestCase
{
    use TestCaseTrait;

    public function testIndexAction()
    {
        static::ensureKernelShutdown();
        $client = static::createClient();
        $client->insulate();

        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $status = self::makeStatus($em);

        $path = "/promises/{$status->getSlug()}";

        $crawler = $client->request('GET', "/${locale}${path}");
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(1, $crawler->filter('body')->count());
    }

    public function testViewActionInactive()
    {
        static::ensureKernelShutdown();
        $client = static::createClient();
        $client->insulate();

        $locale = self::getLocale($client);

        $path = "/promises/~";

        $crawler = $client->request('GET', "/${locale}${path}");
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(1, $crawler->filter('body')->count());
    }

    public function testViewActionPublished()
    {
        static::ensureKernelShutdown();
        $client = static::createClient();
        $client->insulate();

        $em = self::getDoctrine($client);
        $promise = self::makePromise($em);
        $locale = self::getLocale($client);

        $promise->setPublished(true);
        $em->flush();

        $path = "/promise/{$promise->getSlug()}";

        $crawler = $client->request('GET', "/${locale}${path}");
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(1, $crawler->filter('body')->count());
    }

    public function testViewActionNotPublished()
    {
        static::ensureKernelShutdown();
        $client = static::createClient();
        $client->insulate();

        $em = self::getDoctrine($client);
        $promise = self::makePromise($em);
        $locale = self::getLocale($client);

        $promise->setPublished(false);
        $em->flush();

        $path = "/promise/{$promise->getSlug()}";

        $crawler = $client->request('GET', "/${locale}${path}");
        $response = $client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(1, $crawler->filter('body')->count());
    }
}

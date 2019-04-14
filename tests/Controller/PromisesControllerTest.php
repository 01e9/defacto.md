<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;

class PromisesControllerTest extends WebTestCase
{
    use TestCaseTrait;

    public function testViewActionPublished()
    {
        $client = static::createClient();
        $client->insulate();

        $em = self::getDoctrine($client);
        $promise = self::makePromise($em);
        $locale = self::getLocale($client);

        $promise->setPublished(true);
        $em->flush($promise);

        $path = "/pr/{$promise->getSlug()}";

        $crawler = $client->request('GET', "/${locale}${path}");
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(1, $crawler->filter('body')->count());
    }

    public function testViewActionNotPublished()
    {
        $client = static::createClient();
        $client->insulate();

        $em = self::getDoctrine($client);
        $promise = self::makePromise($em);
        $locale = self::getLocale($client);

        $promise->setPublished(false);
        $em->flush($promise);

        $path = "/pr/{$promise->getSlug()}";

        $crawler = $client->request('GET', "/${locale}${path}");
        $response = $client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(1, $crawler->filter('body')->count());
    }
}

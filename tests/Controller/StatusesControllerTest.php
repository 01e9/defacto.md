<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;

class StatusesControllerTest extends WebTestCase
{
    use TestCaseTrait;

    public function testViewAction()
    {
        $client = static::createClient();
        $client->insulate();

        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $status = self::makeStatus($em);

        $path = "/st/{$status->getSlug()}";

        $crawler = $client->request('GET', "/${locale}${path}");
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(1, $crawler->filter('body')->count());
    }

    public function testViewActionInactive()
    {
        $client = static::createClient();
        $client->insulate();

        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $path = "/st/~";

        $crawler = $client->request('GET', "/${locale}${path}");
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(1, $crawler->filter('body')->count());
    }
}

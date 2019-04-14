<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;

class PoliticiansControllerTest extends WebTestCase
{
    use TestCaseTrait;

    public function testViewAction()
    {
        $client = static::createClient();
        $client->insulate();

        $em = self::getDoctrine($client);
        $politician = $this->makePolitician($em);
        $locale = self::getLocale($client);

        $path = "/po/{$politician->getSlug()}";

        $crawler = $client->request('GET', "/${locale}${path}");
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(1, $crawler->filter('body')->count());
    }
}

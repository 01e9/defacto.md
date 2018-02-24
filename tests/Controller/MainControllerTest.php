<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\AppTrait;

class MainControllerTest extends WebTestCase
{
    use AppTrait;

    public function testHomeAction()
    {
        $client = static::createClient();

        // without lang
        {
            $client->restart();
            $client->request('GET', '/');
            $response = $client->getResponse();

            $this->assertEquals(302, $response->getStatusCode());

            $redirectPath = parse_url($response->headers->get('location'), PHP_URL_PATH);
            $this->assertEquals('/'. current(self::getLangs()) .'/', $redirectPath);
        }

        foreach (self::getLangs() as $lang) {
            $client->restart();
            $crawler = $client->request('GET', '/'. $lang .'/');
            $response = $client->getResponse();

            $this->assertEquals(200, $response->getStatusCode());
            $this->assertEquals(1, $crawler->filter('body')->count());
        }
    }
}

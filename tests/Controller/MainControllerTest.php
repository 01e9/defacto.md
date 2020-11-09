<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;

class MainControllerTest extends WebTestCase
{
    use TestCaseTrait;

    public function testHomeAction()
    {
        static::ensureKernelShutdown();
        $client = static::createClient();
        $client->insulate();

        // without lang
        (function () use (&$client) {
            $client->restart();
            $client->request('GET', '/');
            $response = $client->getResponse();

            $this->assertEquals(302, $response->getStatusCode());

            $redirectPath = parse_url($response->headers->get('location'), PHP_URL_PATH);
            $this->assertEquals('/'. current(self::langs()) .'/', $redirectPath);
        })();

        foreach (self::langs() as $lang) {
            (function () use (&$client, &$lang) {
                $client->restart();
                $crawler = $client->request('GET', '/'. $lang .'/');
                $response = $client->getResponse();

                $this->assertEquals(200, $response->getStatusCode());
                $this->assertEquals(1, $crawler->filter('body')->count());
            })();
        }
    }
}

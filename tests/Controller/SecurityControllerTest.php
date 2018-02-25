<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;

class SecurityControllerTest extends WebTestCase
{
    use TestCaseTrait;

    public function testLogin()
    {
        $path = '/login';
        $client = static::createClient();

        // without lang
        {
            $client->restart();
            $client->request('GET', $path);
            $response = $client->getResponse();

            $this->assertEquals(302, $response->getStatusCode());

            $redirectPath = parse_url($response->headers->get('location'), PHP_URL_PATH);
            $this->assertEquals('/'. current(self::getLangs()) . $path, $redirectPath);
        }

        foreach (self::getLangs() as $lang) {
            $client->restart();
            $crawler = $client->request('GET', '/'. $lang . $path);
            $response = $client->getResponse();

            $this->assertEquals(200, $response->getStatusCode());
            $this->assertGreaterThanOrEqual(1, $crawler->filter('form')->count());

            $client->restart();
            self::logInClientAsRole($client, 'ROLE_USER');
            $client->request('GET', '/'. $lang . $path);
            $response = $client->getResponse();

            $this->assertEquals(302, $response->getStatusCode());
            $redirectPath = parse_url($response->headers->get('location'), PHP_URL_PATH);
            $this->assertEquals('/'. $lang . '/', $redirectPath);
        }
    }
}

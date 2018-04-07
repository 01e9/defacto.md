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

        $status = $client->getContainer()->get('doctrine.orm.default_entity_manager')
            ->getRepository('App:Status')->findOneBy([]);

        $this->assertNotNull($status);

        $path = '/s/'. $status->getSlug();
        $pathInactive = '/s/~';

        // without lang
        (function () use (&$client, &$path) {
            $client->restart();
            $client->request('GET', $path);
            $response = $client->getResponse();

            $this->assertEquals(302, $response->getStatusCode());

            $redirectPath = parse_url($response->headers->get('location'), PHP_URL_PATH);
            $this->assertEquals('/'. current(self::getLangs()) . $path, $redirectPath);
        })();

        foreach (self::getLangs() as $lang) {
            (function () use (&$client, &$lang, &$path) {
                $client->restart();
                $crawler = $client->request('GET', '/'. $lang . $path);
                $response = $client->getResponse();

                $this->assertEquals(200, $response->getStatusCode());
                $this->assertEquals(1, $crawler->filter('body')->count());
            })();
            (function () use (&$client, &$lang, &$pathInactive) {
                $client->restart();
                $crawler = $client->request('GET', '/'. $lang . $pathInactive);
                $response = $client->getResponse();

                $this->assertEquals(200, $response->getStatusCode());
                $this->assertEquals(1, $crawler->filter('body')->count());
            })();
        }
    }
}

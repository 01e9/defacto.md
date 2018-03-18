<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;

class PromisesControllerTest extends WebTestCase
{
    use TestCaseTrait;

    public function testViewAction()
    {
        $client = static::createClient();
        $client->insulate();

        $repository = $client->getContainer()->get('doctrine.orm.default_entity_manager')
            ->getRepository('App:Promise');

        $promisePublished = $repository->findOneBy(['published' => true]);
        $promiseUnpublished = $repository->findOneBy(['published' => false]);

        $this->assertNotNull($promisePublished);
        $this->assertNotNull($promiseUnpublished);

        $pathPublished = '/promise/'. $promisePublished->getSlug();
        $pathUnpublished = '/promise/'. $promiseUnpublished->getSlug();

        // without lang
        foreach ([$pathPublished, $pathUnpublished] as $path) {
            (function () use (&$client, &$path) {
                $client->restart();
                $client->request('GET', $path);
                $response = $client->getResponse();

                $this->assertEquals(302, $response->getStatusCode());

                $redirectPath = parse_url($response->headers->get('location'), PHP_URL_PATH);
                $this->assertEquals('/'. current(self::getLangs()) . $path, $redirectPath);
            })();
        }

        foreach (self::getLangs() as $lang) {
            (function () use (&$client, &$lang, &$pathPublished) {
                $client->restart();
                $crawler = $client->request('GET', '/'. $lang . $pathPublished);
                $response = $client->getResponse();

                $this->assertEquals(200, $response->getStatusCode());
                $this->assertEquals(1, $crawler->filter('body')->count());
            })();
            (function () use (&$client, &$lang, &$pathUnpublished) {
                $client->restart();
                $crawler = $client->request('GET', '/'. $lang . $pathUnpublished);
                $response = $client->getResponse();

                $this->assertEquals(404, $response->getStatusCode());
                $this->assertEquals(1, $crawler->filter('body')->count());
            })();
        }
    }
}

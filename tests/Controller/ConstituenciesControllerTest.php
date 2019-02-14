<?php

namespace App\Tests\Controller;

use App\Entity\Candidate;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;

class ConstituenciesControllerTest extends WebTestCase
{
    use TestCaseTrait;

    public function testViewAction()
    {
        $client = static::createClient();
        $client->insulate();

        $em = $client->getContainer()->get('doctrine.orm.default_entity_manager');
        $candidate = $this->createCandidate($em);

        $path = '/c/'. $candidate->getConstituency()->getSlug() .'/'. $candidate->getElection()->getSlug();

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
        }
    }
}

<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;

class ConstituenciesControllerTest extends WebTestCase
{
    use TestCaseTrait;

    public function testViewElectionAction()
    {
        static::ensureKernelShutdown();
        $client = static::createClient();
        $client->insulate();

        $em = self::getDoctrine($client);
        $candidate = $this->makeCandidate($em);
        $locale = self::getLocale($client);

        $path = "/constituency/{$candidate->getConstituency()->getSlug()}/{$candidate->getElection()->getSlug()}";

        $crawler = $client->request('GET', "/${locale}${path}");
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(1, $crawler->filter('body')->count());
    }
}

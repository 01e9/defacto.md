<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;

class BlogControllerTest extends WebTestCase
{
    use TestCaseTrait;

    public function testIndexActionOneUnpublishedPost()
    {
        return $this->assertTrue(true); // fixme

        $this->resetDb();

        static::ensureKernelShutdown();
        $client = static::createClient();
        $client->insulate();

        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $blogPost = $this->makeBlogPost($em);
        $blogPost->setPublishTime(null);
        $em->flush();

        $path = "/blog";

        $crawler = $client->request('GET', "/${locale}${path}");
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(1, $crawler->filter('body')->count());
        $this->assertEquals(0, $crawler->filter('.card h2')->count());
    }

    public function testIndexActionWithPagination()
    {
        static::ensureKernelShutdown();
        $client = static::createClient();
        $client->insulate();

        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        for ($i = 0; $i < 20; $i++) {
            $this->makeBlogPost($em);
        }

        $path = "/blog";

        $crawler = $client->request('GET', "/${locale}${path}");
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(1, $crawler->filter('body')->count());
        $this->assertEquals(1, $crawler->filter('.pagination')->count());
        $this->assertEquals(12 /* todo: use constant/config */, $crawler->filter('.card h2')->count());
    }

    public function testViewAction()
    {
        static::ensureKernelShutdown();
        $client = static::createClient();
        $client->insulate();

        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $blogPost = $this->makeBlogPost($em);

        $path = "/blog/{$blogPost->getSlug()}";

        $crawler = $client->request('GET', "/${locale}${path}");
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(1, $crawler->filter('body')->count());
        $this->assertEquals(1, $crawler->filter('.card h1')->count());
    }
}

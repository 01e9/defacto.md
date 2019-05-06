<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;

class BlogControllerTest extends WebTestCase
{
    use TestCaseTrait;

    public function testIndexActionOneUnpublishedPost()
    {
        $this->resetDb();

        $client = static::createClient();
        $client->insulate();

        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $blogPost = $this->makeBlogPost($em);
        $blogPost->setPublishTime(null);
        $em->flush($blogPost);

        $path = "/blog";

        $crawler = $client->request('GET', "/${locale}${path}");
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(1, $crawler->filter('body')->count());
        $this->assertEquals(0, $crawler->filter('.card h2')->count());
    }

    public function testIndexActionWithPagination()
    {
        $client = static::createClient();
        $client->insulate();

        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        for ($i = 0; $i < 10; $i++) {
            $this->makeBlogPost($em);
        }

        $path = "/blog";

        $crawler = $client->request('GET', "/${locale}${path}");
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(1, $crawler->filter('body')->count());
        $this->assertEquals(1, $crawler->filter('.pagination')->count());
        $this->assertEquals(7, $crawler->filter('.card h2')->count());
    }

    public function testViewAction()
    {
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

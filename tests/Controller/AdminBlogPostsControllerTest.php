<?php

namespace App\Tests\Controller;

use App\Entity\BlogPost;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;

class AdminBlogPostsControllerTest extends WebTestCase
{
    use TestCaseTrait;

    //region Index

    public function testIndexActionAccess()
    {
        $this->assertOnlyAdminCanAccess('/admin/blog-posts');
    }

    //endregion

    //region Add

    public function testAddActionAccess()
    {
        $this->assertOnlyAdminCanAccess('/admin/blog-posts/add');
    }

    public function testAddActionSubmitInvalidData()
    {
        $client = self::createAdminClient();
        $locale = self::getLocale($client);

        $form = $client
            ->request('GET', "/${locale}/admin/blog-posts/add")
            ->filter('form')->form();
        $client->submit($form, []);

        $this->assertHasFormErrors($client->getResponse());
    }

    public function testAddActionSubmitValidData()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);
        $random = self::randomNumber();

        $formData = [
            'blog_post[title]' => "Test ${random}",
            'blog_post[slug]' => "test-${random}",
            'blog_post[content]' => "Test ${random} ". str_repeat("Hello World ", 10),
        ];

        $form = $client
            ->request('GET', "/${locale}/admin/blog-posts/add")
            ->filter('form')->form();
        $client->submit($form, $formData);
        $route = $this->assertRedirectsToRoute($client->getResponse(), 'admin_blog_post_edit');

        $em->clear('App:BlogPost');
        /** @var BlogPost $blogPost */
        $blogPost = $em->getRepository('App:BlogPost')->find($route['id']);

        $this->assertNotNull($blogPost);
        $this->assertEquals($formData['blog_post[title]'], $blogPost->getTitle());
        $this->assertEquals($formData['blog_post[slug]'], $blogPost->getSlug());

        self::cleanup($em);
    }

    //endregion

    //region Edit

    public function testEditActionAccess()
    {
        $client = static::createClient();
        $client->insulate();

        $em = self::getDoctrine($client);
        $blogPost = $this->makeBlogPost($em);

        $this->assertOnlyAdminCanAccess("/admin/blog-posts/{$blogPost->getId()}", $client);
    }

    public function testEditActionSubmitInvalidData()
    {
        $client = static::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $blogPost = $this->makeBlogPost($em);
        $form = $client
            ->request('GET', "/${locale}/admin/blog-posts/{$blogPost->getId()}")
            ->filter('form')->form();
        $client->submit($form, ['blog_post[title]' => '?',]);

        $this->assertHasFormErrors($client->getResponse());

        self::cleanup($em);
    }

    public function testEditActionSubmitValidData()
    {
        $client = static::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);
        $random = self::randomNumber();

        $formData = [
            'blog_post[title]' => "Test ${random}",
            'blog_post[slug]' => "test-${random}",
            'blog_post[content]' => "Test ${random} ". str_repeat("Hello World ", 10),
            'blog_post[publishTime]' => '01.12.2010',
        ];

        $blogPost = $this->makeBlogPost($em);
        $form = $client
            ->request('GET', "/${locale}/admin/blog-posts/{$blogPost->getId()}")
            ->filter('form')->form();
        $client->submit($form, $formData);
        $this->assertRedirectsToRoute($client->getResponse(), 'admin_blog_post_edit');

        $em->clear('App:BlogPost');
        /** @var BlogPost $blogPost */
        $blogPost = $em->getRepository('App:BlogPost')->find($blogPost->getId());

        $this->assertNotNull($blogPost);
        $this->assertEquals($formData['blog_post[title]'], $blogPost->getTitle());
        $this->assertEquals($formData['blog_post[slug]'], $blogPost->getSlug());

        self::cleanup($em);
    }

    //endregion

    //region Delete

    public function testDeleteActionAccess()
    {
        $client = static::createClient();
        $client->insulate();

        $em = self::getDoctrine($client);
        $blogPost = $this->makeBlogPost($em);

        $this->assertOnlyAdminCanAccess("/admin/blog-posts/{$blogPost->getId()}/d", $client);

        self::cleanup($em);
    }

    public function testDeleteActionSubmit()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $blogPost = $this->makeBlogPost($em);

        $form = $client
            ->request('GET', "/${locale}/admin/blog-posts/{$blogPost->getId()}/d")
            ->filter('form')->form();
        $client->submit($form);
        $this->assertRedirectsToRoute($client->getResponse(), 'admin_blog_posts');

        $em->clear('App:BlogPost');
        /** @var BlogPost $blogPost */
        $blogPost = $em->getRepository('App:BlogPost')->find($blogPost->getId());

        $this->assertNull($blogPost);

        self::cleanup($em);
    }

    //endregion
}
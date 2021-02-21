<?php

namespace App\Tests\Factory\Entity;

use App\Entity\BlogPost;
use App\Tests\TestContainer;

class BlogPostFactory
{
    const PUBLISH_TIME = 'publish_time';
    const TITLE = 'title';
    const SLUG = 'slug';
    const CATEGORY = 'category';
    const CONTENT = 'content';
    const IMAGE = 'image';

    public static function create(TestContainer $container, array $params = []): BlogPost
    {
        $entity = new BlogPost();
        $faker = $container->faker();

        $entity->setPublishTime($params[self::PUBLISH_TIME] ?? $entity->getPublishTime());
        $entity->setTitle($params[self::TITLE] ?? $faker->words(3, true));
        $entity->setSlug($params[self::SLUG] ?? $faker->unique()->slug);
        $entity->setCategory($params[self::CATEGORY] ?? $entity->getCategory());
        $entity->setContent($params[self::CONTENT] ?? $faker->text());
        $entity->setImage($params[self::IMAGE] ?? $entity->getImage());

        return $entity;
    }
}
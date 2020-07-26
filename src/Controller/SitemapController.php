<?php

namespace App\Controller;

use App\Repository\BlogPostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * @Route(defaults={"_format"="xml"}, methods={"GET"})
 */
class SitemapController extends AbstractController
{
    private $blogPostRepository;

    public function __construct(
        BlogPostRepository $blogPostRepository
    )
    {
        $this->blogPostRepository = $blogPostRepository;
    }

    /**
     * @Route(path="/sitemap.xml", name="sitemap")
     */
    public function indexAction(Request $request, RouterInterface $router)
    {
        $blogLastModified = ($post = $this->blogPostRepository->findOneBy([], ['publishTime' => 'DESC']))
            ? $post->getPublishTime() : null;

        return $this->render('app/sitemap/index.xml.twig', [
            'blogLastModified' => $blogLastModified,
        ]);
    }

    /**
     * @Route(path="/sitemap-blog.xml", name="sitemap_blog")
     */
    public function blogAction(Request $request)
    {
        $posts = $this->blogPostRepository->createQueryBuilder('bp')
            ->select('bp')
            ->where('bp.publishTime IS NOT NULL')
            ->orderBy('bp.publishTime', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('app/sitemap/blog.xml.twig', ['posts' => $posts]);
    }
}
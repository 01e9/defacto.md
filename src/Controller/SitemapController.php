<?php

namespace App\Controller;

use App\Repository\BlogPostRepository;
use App\Repository\MandateRepository;
use App\Repository\PoliticianRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * @Route(defaults={"_format"="xml"}, methods={"GET"})
 */
class SitemapController extends AbstractController
{
    private BlogPostRepository $blogPostRepository;
    private PoliticianRepository $politicianRepository;
    private MandateRepository $mandateRepository;

    public function __construct(
        BlogPostRepository $blogPostRepository,
        PoliticianRepository $politicianRepository,
        MandateRepository $mandateRepository
    )
    {
        $this->blogPostRepository = $blogPostRepository;
        $this->politicianRepository = $politicianRepository;
        $this->mandateRepository = $mandateRepository;
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
     * @Route(path="/sitemap.blog.xml", name="sitemap_blog")
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

    /**
     * @Route(path="/sitemap.politicians.xml", name="sitemap_politicians")
     */
    public function politiciansAction(Request $request)
    {
        $politicians = $this->politicianRepository->createQueryBuilder('p')
            ->select('p')
            ->orderBy('p.birthDate', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('app/sitemap/politicians.xml.twig', ['politicians' => $politicians]);
    }

    /**
     * @Route(path="/sitemap.mandates.xml", name="sitemap_mandates")
     */
    public function mandatesAction(Request $request)
    {
        $mandates = $this->mandateRepository->createQueryBuilder('m')
            ->select('m')
            ->orderBy('m.beginDate', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('app/sitemap/mandates.xml.twig', ['mandates' => $mandates]);
    }
}
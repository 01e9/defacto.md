<?php

namespace App\Controller;

use App\Entity\Constituency;
use App\Repository\BlogPostRepository;
use App\Repository\ConstituencyRepository;
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
    private ConstituencyRepository $constituencyRepository;

    public function __construct(
        BlogPostRepository $blogPostRepository,
        PoliticianRepository $politicianRepository,
        MandateRepository $mandateRepository,
        ConstituencyRepository $constituencyRepository
    )
    {
        $this->blogPostRepository = $blogPostRepository;
        $this->politicianRepository = $politicianRepository;
        $this->mandateRepository = $mandateRepository;
        $this->constituencyRepository = $constituencyRepository;
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

    /**
     * @Route(path="/sitemap.constituencies.xml", name="sitemap_constituencies")
     */
    public function constituenciesAction(Request $request)
    {
        $constituenciesElections = [];
        foreach (
            $this->constituencyRepository->createQueryBuilder('c')
                ->select('c')
                ->leftJoin('c.mandates', 'ma')
                ->leftJoin('c.candidates', 'ca')
                ->orderBy('c.name', 'ASC')
                ->getQuery()
                ->getResult()
            as $constituency /** @var Constituency $constituency */
        ) {
            foreach ($constituency->getCandidates() as $candidate) {
                $constituenciesElections[ $constituency->getSlug() ][ $candidate->getElection()->getSlug() ] = true;
            }
            foreach ($constituency->getMandates() as $mandate) {
                $constituenciesElections[ $constituency->getSlug() ][ $mandate->getElection()->getSlug() ] = true;
            }
        }

        return $this->render('app/sitemap/constituencies.xml.twig', [
            'constituenciesElections' => $constituenciesElections
        ]);
    }
}
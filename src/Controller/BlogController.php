<?php

namespace App\Controller;

use App\Repository\BlogPostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    private $paginator;
    private $blogPostRepository;

    public function __construct(
        PaginatorInterface $paginator,
        BlogPostRepository $blogPostRepository
    )
    {
        $this->paginator = $paginator;
        $this->blogPostRepository = $blogPostRepository;
    }

    /**
     * @Route(
     *     path="/blog",
     *     name="blog",
     *     methods={"GET"},
     *     defaults={}
     * )
     */
    public function indexAction(Request $request)
    {
        $blogPostsQuery = $this->blogPostRepository->createQueryBuilder('p')
            ->andWhere('p.publishTime IS NOT NULL')
            ->orderBy('p.publishTime', 'DESC');

        $blogPosts = $this->paginator->paginate(
            $blogPostsQuery,
            $request->query->getInt('page', 1), // fixme: hardcode
            7 // fixme: hardcode
        );

        return $this->render('app/page/blog.html.twig', [
            'blogPosts' => $blogPosts,
        ]);
    }

    /**
     * @Route(
     *     path="/blog/{slug}",
     *     name="blog_post",
     *     methods={"GET"},
     *     defaults={}
     * )
     */
    public function viewAction(Request $request, string $slug)
    {
        $blogPost = $this->blogPostRepository->findOneBy(['slug' => $slug]);
        if (!$blogPost) {
            throw $this->createNotFoundException();
        }

        return $this->render('app/page/blog-post.html.twig', [
            'blogPost' => $blogPost,
        ]);
    }
}
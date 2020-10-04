<?php

namespace App\Controller;

use App\Consts;
use App\Repository\BlogCategoryRepository;
use App\Repository\BlogPostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    private PaginatorInterface $paginator;
    private BlogPostRepository $blogPostRepository;
    private BlogCategoryRepository $blogCategoryRepository;

    public function __construct(
        PaginatorInterface $paginator,
        BlogPostRepository $blogPostRepository,
        BlogCategoryRepository $blogCategoryRepository
    )
    {
        $this->paginator = $paginator;
        $this->blogPostRepository = $blogPostRepository;
        $this->blogCategoryRepository = $blogCategoryRepository;
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
        if ($categorySlug = $request->query->get(Consts::QUERY_PARAM_CATEGORY)) {
            $blogPostsQuery
                ->innerJoin('p.category', 'pc', 'WITH', 'pc.slug = :categorySlug')
                ->setParameter('categorySlug', $categorySlug)
            ;
        }

        $blogPosts = $this->paginator->paginate(
            $blogPostsQuery,
            $request->query->getInt(Consts::QUERY_PARAM_PAGE, 1),
            Consts::PAGINATION_SIZE_BLOG
        );

        return $this->render('app/page/blog.html.twig', [
            'blogPosts' => $blogPosts,
            'categories' => $this->blogCategoryRepository->findWithPosts(),
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
        if (!$blogPost || !$blogPost->getPublishTime()) {
            throw $this->createNotFoundException();
        }

        return $this->render('app/page/blog-post.html.twig', [
            'blogPost' => $blogPost,
        ]);
    }
}
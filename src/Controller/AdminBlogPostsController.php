<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Form\BlogPostDeleteType;
use App\Form\BlogPostType;
use App\Repository\BlogCategoryRepository;
use App\Repository\BlogPostRepository;
use App\Service\EntityFileUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route(path="/admin/blog-posts")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminBlogPostsController extends AbstractController
{
    private EntityFileUploader $entityFileUploader;
    private BlogPostRepository $blogPostsRepository;
    private BlogCategoryRepository $blogCategoryRepository;

    public function __construct(
        BlogPostRepository $blogPostsRepository,
        BlogCategoryRepository $blogCategoryRepository,
        EntityFileUploader $entityFileUploader
    )
    {
        $this->blogPostsRepository = $blogPostsRepository;
        $this->blogCategoryRepository = $blogCategoryRepository;
        $this->entityFileUploader = $entityFileUploader;
    }

    /**
     * @Route(path="", name="admin_blog_posts")
     */
    public function indexAction(Request $request): Response
    {
        $pagination = $this->blogPostsRepository->getAdminListPaginated($request);

        return $this->render('admin/page/blogPost/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route(path="/add", name="admin_blog_post_add")
     * @return Response
     */
    public function addAction(Request $request, TranslatorInterface $translator)
    {
        $blogPost = new BlogPost();

        $form = $this->createForm(BlogPostType::class, $blogPost, [
            'categories' => $this->blogCategoryRepository->getAdminChoices(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var BlogPost $blogPost */
            $blogPost = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($blogPost);
            $em->flush();

            /** @var UploadedFile $imageFile */
            if ($imageFile = $form->get('imageUpload')->getData()) {
                $this->entityFileUploader->uploadAndUpdate($blogPost, $imageFile);
            }

            $this->addFlash('success', $translator->trans('flash.blog_post_created'));

            return $this->redirectToRoute('admin_blog_post_edit', ['id' => $blogPost->getId()]);
        }

        return $this->render('admin/page/blogPost/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/{id}", name="admin_blog_post_edit")
     * @return Response
     */
    public function editAction(Request $request, string $id, TranslatorInterface $translator)
    {
        /** @var BlogPost $blogPost */
        $blogPost = $this->getDoctrine()->getRepository('App:BlogPost')->find($id);
        if (!$blogPost) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(BlogPostType::class, $blogPost, [
            'categories' => $this->blogCategoryRepository->getAdminChoices(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var BlogPost $blogPost */
            $blogPost = $form->getData();

            $em = $this->getDoctrine()->getManager();

            $em->persist($blogPost);
            $em->flush();

            /** @var UploadedFile $imageFile */
            if ($imageFile = $form->get('imageUpload')->getData()) {
                $this->entityFileUploader->uploadAndUpdate($blogPost, $imageFile);
            }

            $this->addFlash('success', $translator->trans('flash.blog_post_updated'));

            return $this->redirectToRoute('admin_blog_post_edit', ['id' => $blogPost->getId()]);
        }

        return $this->render('admin/page/blogPost/edit.html.twig', [
            'form' => $form->createView(),
            'blogPost' => $blogPost,
        ]);
    }

    /**
     * @Route(path="/{id}/d", name="admin_blog_post_delete", methods={"GET","POST"})
     */
    public function deleteAction(Request $request, string $id, TranslatorInterface $translator)
    {
        $blogPost = $this->getDoctrine()->getRepository('App:BlogPost')->find($id);
        if (!$blogPost) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(BlogPostDeleteType::class, $blogPost, [])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var BlogPost $blogPost */
            $blogPost = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->remove($blogPost);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.blog_post_deleted'));

            return $this->redirectToRoute('admin_blog_posts');
        }

        return $this->render('admin/page/blogPost/delete.html.twig', [
            'form' => $form->createView(),
            'blogPost' => $blogPost,
        ]);
    }
}
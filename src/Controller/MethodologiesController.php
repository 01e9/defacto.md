<?php

namespace App\Controller;

use App\Repository\MethodologyRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MethodologiesController extends AbstractController
{
    private PaginatorInterface $paginator;
    private MethodologyRepository $repository;

    public function __construct(PaginatorInterface $paginator, MethodologyRepository $repository)
    {
        $this->paginator = $paginator;
        $this->repository = $repository;
    }

    /**
     * @Route(
     *     path="/methodologies",
     *     name="methodologies",
     *     methods={"GET"},
     *     defaults={}
     * )
     */
    public function indexAction(Request $request)
    {
        return $this->render('app/page/methodologies.html.twig', [
            'methodologies' => $this->repository->findAll(),
        ]);
    }

    /**
     * @Route(
     *     path="/methodology/{slug}",
     *     name="methodology",
     *     methods={"GET"},
     *     defaults={}
     * )
     */
    public function viewAction(Request $request, string $slug)
    {
        $methodology = $this->repository->findOneBy(['slug' => $slug]);
        if (!$methodology) {
            throw $this->createNotFoundException();
        }

        return $this->render('app/page/methodology.html.twig', [
            'methodology' => $methodology,
        ]);
    }
}
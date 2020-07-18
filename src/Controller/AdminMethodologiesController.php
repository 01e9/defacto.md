<?php

namespace App\Controller;

use App\Entity\Methodology;
use App\Form\MethodologyDeleteType;
use App\Form\MethodologyType;
use App\Repository\MethodologyRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route(path="/admin/methodologies")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminMethodologiesController extends AbstractController
{
    private MethodologyRepository $repository;

    public function __construct(MethodologyRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route(path="", name="admin_methodologies")
     */
    public function indexAction(Request $request): Response
    {
        $pagination = $this->repository->getAdminListPaginated($request);

        return $this->render('admin/page/methodology/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route(path="/add", name="admin_methodology_add")
     * @return Response
     */
    public function addAction(Request $request, TranslatorInterface $translator)
    {
        $methodology = new Methodology();

        $form = $this->createForm(MethodologyType::class, $methodology, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Methodology $methodology */
            $methodology = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($methodology);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.methodology_created'));

            return $this->redirectToRoute('admin_methodology_edit', ['id' => $methodology->getId()]);
        }

        return $this->render('admin/page/methodology/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/{id}", name="admin_methodology_edit")
     * @return Response
     */
    public function editAction(Request $request, string $id, TranslatorInterface $translator)
    {
        /** @var Methodology $methodology */
        $methodology = $this->repository->find($id);
        if (!$methodology) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(MethodologyType::class, $methodology, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Methodology $methodology */
            $methodology = $form->getData();

            $em = $this->getDoctrine()->getManager();

            $em->persist($methodology);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.methodology_updated'));

            return $this->redirectToRoute('admin_methodology_edit', ['id' => $methodology->getId()]);
        }

        return $this->render('admin/page/methodology/edit.html.twig', [
            'form' => $form->createView(),
            'methodology' => $methodology,
        ]);
    }

    /**
     * @Route(path="/{id}/d", name="admin_methodology_delete", methods={"GET","POST"})
     */
    public function deleteAction(Request $request, string $id, TranslatorInterface $translator)
    {
        $methodology = $this->repository->find($id);
        if (!$methodology) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(MethodologyDeleteType::class, $methodology, [])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Methodology $methodology */
            $methodology = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->remove($methodology);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.methodology_deleted'));

            return $this->redirectToRoute('admin_methodologies');
        }

        return $this->render('admin/page/methodology/delete.html.twig', [
            'form' => $form->createView(),
            'methodology' => $methodology,
        ]);
    }
}
<?php

namespace App\Controller;

use App\Entity\Competence;
use App\Form\CompetenceDeleteType;
use App\Form\CompetenceType;
use App\Repository\CompetenceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route(path="/admin/competences")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminCompetencesController extends AbstractController
{
    private CompetenceRepository $repository;

    /**
     * @required
     */
    public function setRequirements(CompetenceRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route(path="", name="admin_competences")
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $competences = $this->repository->getAdminList($request);

        return $this->render('admin/page/competence/index.html.twig', [
            'competences' => $competences,
        ]);
    }

    /**
     * @Route(path="/add", name="admin_competence_add")
     * @return Response
     */
    public function addAction(Request $request, TranslatorInterface $translator)
    {
        $form = $this->createForm(CompetenceType::class, null, [])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Competence $competence */
            $competence = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($competence);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.competence_created'));

            return $this->redirectToRoute('admin_competence_edit', ['id' => $competence->getId()]);
        }

        return $this->render('admin/page/competence/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/{id}", name="admin_competence_edit")
     * @return Response
     */
    public function editAction(Request $request, string $id, TranslatorInterface $translator)
    {
        $competence = $this->repository->find($id);
        if (!$competence) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(CompetenceType::class, $competence, [])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Competence $competence */
            $competence = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($competence);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.competence_updated'));

            return $this->redirectToRoute('admin_competence_edit', ['id' => $competence->getId()]);
        }

        return $this->render('admin/page/competence/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/{id}/d", name="admin_competence_delete", methods={"GET","POST"})
     */
    public function deleteAction(Request $request, string $id, TranslatorInterface $translator)
    {
        $competence = $this->repository->find($id);
        if (!$competence) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(CompetenceDeleteType::class, $competence, [])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Competence $competence */
            $competence = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->remove($competence);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.competence_deleted'));

            return $this->redirectToRoute('admin_competences');
        }

        return $this->render('admin/page/competence/delete.html.twig', [
            'form' => $form->createView(),
            'competence' => $competence,
        ]);
    }
}
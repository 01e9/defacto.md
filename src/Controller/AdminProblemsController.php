<?php

namespace App\Controller;

use App\Entity\Problem;
use App\Form\ProblemDeleteType;
use App\Form\ProblemType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route(path="/admin/problems")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminProblemsController extends AbstractController
{
    /**
     * @Route(path="", name="admin_problems")
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $problems = $this->getDoctrine()->getRepository('App:Problem')->getAdminList($request);

        return $this->render('admin/page/problem/index.html.twig', [
            'problems' => $problems,
        ]);
    }

    /**
     * @Route(path="/add", name="admin_problem_add")
     * @return Response
     */
    public function addAction(Request $request, TranslatorInterface $translator)
    {
        $form = $this->createForm(ProblemType::class, null, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Problem $problem */
            $problem = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($problem);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.problem_created'));

            return $this->redirectToRoute('admin_problem_edit', ['id' => $problem->getId()]);
        }

        return $this->render('admin/page/problem/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/{id}", name="admin_problem_edit")
     * @return Response
     */
    public function editAction(Request $request, string $id, TranslatorInterface $translator)
    {
        $problem = $this->getDoctrine()->getRepository('App:Problem')->find($id);

        if (!$problem) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(ProblemType::class, $problem, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Problem $problem */
            $problem = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($problem);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.problem_updated'));

            return $this->redirectToRoute('admin_problem_edit', ['id' => $problem->getId()]);
        }

        return $this->render('admin/page/problem/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/{id}/d", name="admin_problem_delete", methods={"GET","POST"})
     */
    public function deleteAction(Request $request, string $id, TranslatorInterface $translator)
    {
        $problem = $this->getDoctrine()->getRepository('App:Problem')->find($id);
        if (!$problem) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(ProblemDeleteType::class, $problem, [])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Problem $problem */
            $problem = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->remove($problem);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.problem_deleted'));

            return $this->redirectToRoute('admin_problems');
        }

        return $this->render('admin/page/problem/delete.html.twig', [
            'form' => $form->createView(),
            'problem' => $problem,
        ]);
    }
}
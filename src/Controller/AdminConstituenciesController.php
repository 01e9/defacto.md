<?php


namespace App\Controller;

use App\Entity\Constituency;
use App\Form\ConstituencyDeleteType;
use App\Form\ConstituencyType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Route(path="/admin/constituencies")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminConstituenciesController extends AbstractController
{
    /**
     * @Route(path="", name="admin_constituencies")
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $constituencies = $this->getDoctrine()->getRepository('App:Constituency')->getAdminList($request);

        return $this->render('admin/page/constituency/index.html.twig', [
            'constituencies' => $constituencies,
        ]);
    }

    /**
     * @Route(path="/add", name="admin_constituency_add")
     * @return Response
     */
    public function addAction(Request $request, TranslatorInterface $translator)
    {
        $form = $this->createForm(ConstituencyType::class, null, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Constituency $constituency */
            $constituency = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($constituency);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.constituency_created'));

            return $this->redirectToRoute('admin_constituency_edit', ['id' => $constituency->getId()]);
        }

        return $this->render('admin/page/constituency/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/{id}", name="admin_constituency_edit")
     * @return Response
     */
    public function editAction(Request $request, string $id, TranslatorInterface $translator)
    {
        $constituency = $this->getDoctrine()->getRepository('App:Constituency')->find($id);
        if (!$constituency) {
            throw $this->createNotFoundException();
        }

        $originalProblems = new ArrayCollection();
        array_map([$originalProblems, 'add'], $constituency->getProblems()->toArray());

        $originalCandidates = new ArrayCollection();
        array_map([$originalCandidates, 'add'], $constituency->getCandidates()->toArray());

        $originalCandidateProblemOpinions = new ArrayCollection();
        array_map([$originalCandidateProblemOpinions, 'add'], $constituency->getCandidateProblemOpinions()->toArray());

        $form = $this->createForm(ConstituencyType::class, $constituency, [
            'constituencies' => ['~' => $constituency],
            'elections' => $this->getDoctrine()->getRepository('App:Election')->getAdminChoices(),
            'problems' => $this->getDoctrine()->getRepository('App:Problem')->getAdminChoices(),
            'politicians' => $this->getDoctrine()->getRepository('App:Politician')->getAdminChoices(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Constituency $constituency */
            $constituency = $form->getData();

            $em = $this->getDoctrine()->getManager();

            foreach ($originalProblems as $problem) {
                if (false === $constituency->getProblems()->contains($problem)) {
                    $constituency->getProblems()->removeElement($problem);
                    $em->remove($problem);
                }
            }
            foreach ($originalCandidates as $candidate) {
                if (false === $constituency->getCandidates()->contains($candidate)) {
                    $constituency->getCandidates()->removeElement($candidate);
                    $em->remove($candidate);
                }
            }
            foreach ($originalCandidateProblemOpinions as $opinion) {
                if (false === $constituency->getCandidateProblemOpinions()->contains($opinion)) {
                    $constituency->getCandidateProblemOpinions()->removeElement($opinion);
                    $em->remove($opinion);
                }
            }

            $em->persist($constituency);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.constituency_updated'));

            return $this->redirectToRoute('admin_constituencies');
        }

        return $this->render('admin/page/constituency/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/{id}/d", name="admin_constituency_delete", methods={"GET","POST"})
     */
    public function deleteAction(Request $request, string $id, TranslatorInterface $translator)
    {
        $constituency = $this->getDoctrine()->getRepository('App:Constituency')->find($id);
        if (!$constituency) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(ConstituencyDeleteType::class, $constituency, [])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Constituency $constituency */
            $constituency = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->remove($constituency);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.constituency_deleted'));

            return $this->redirectToRoute('admin_constituencies');
        }

        return $this->render('admin/page/constituency/delete.html.twig', [
            'form' => $form->createView(),
            'constituency' => $constituency,
        ]);
    }
}
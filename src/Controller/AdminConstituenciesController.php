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

        $form = $this->createForm(ConstituencyType::class, $constituency, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Constituency $constituency */
            $constituency = $form->getData();

            $em = $this->getDoctrine()->getManager();
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
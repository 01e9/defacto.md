<?php

namespace App\Controller;

use App\Entity\Party;
use App\Form\PartyDeleteType;
use App\Form\PartyType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route(path="/admin/parties")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminPartiesController extends AbstractController
{
    /**
     * @Route(path="", name="admin_parties")
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $parties = $this->getDoctrine()->getRepository('App:Party')->getAdminList($request);

        return $this->render('admin/page/party/index.html.twig', [
            'parties' => $parties,
        ]);
    }

    /**
     * @Route(path="/add", name="admin_party_add")
     * @return Response
     */
    public function addAction(Request $request, TranslatorInterface $translator)
    {
        $form = $this->createForm(PartyType::class, null, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Party $party */
            $party = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($party);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.party_created'));

            return $this->redirectToRoute('admin_party_edit', ['id' => $party->getId()]);
        }

        return $this->render('admin/page/party/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/{id}", name="admin_party_edit")
     * @return Response
     */
    public function editAction(Request $request, string $id, TranslatorInterface $translator)
    {
        $party = $this->getDoctrine()->getRepository('App:Party')->find($id);

        if (!$party) {
            throw $this->createNotFoundException();
        }

        /** @var File $initialLogo */
        $initialLogo = $party->getLogo();

        $form = $this->createForm(PartyType::class, $party, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Party $party */
            $party = $form->getData();

            if (!$party->getLogo()) {
                $party->setLogo($initialLogo);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($party);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.party_updated'));

            return $this->redirectToRoute('admin_party_edit', ['id' => $party->getId()]);
        }

        return $this->render('admin/page/party/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/{id}/d", name="admin_party_delete", methods={"GET","POST"})
     */
    public function deleteAction(Request $request, string $id, TranslatorInterface $translator)
    {
        $party = $this->getDoctrine()->getRepository('App:Party')->find($id);
        if (!$party) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(PartyDeleteType::class, $party, [])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Party $party */
            $party = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->remove($party);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.party_deleted'));

            return $this->redirectToRoute('admin_parties');
        }

        return $this->render('admin/page/party/delete.html.twig', [
            'form' => $form->createView(),
            'party' => $party,
        ]);
    }
}
<?php


namespace App\Controller;

use App\Entity\Mandate;
use App\Form\MandateDeleteType;
use App\Form\MandateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route(path="/admin/mandates")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminMandatesController extends AbstractController
{
    /**
     * @Route(path="", name="admin_mandates")
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $mandates = $this->getDoctrine()->getRepository('App:Mandate')->getAdminList($request);

        return $this->render('admin/page/mandate/index.html.twig', [
            'mandates' => $mandates,
        ]);
    }

    /**
     * @Route(path="/add", name="admin_mandate_add")
     * @return Response
     */
    public function addAction(Request $request, TranslatorInterface $translator)
    {
        $form = $this->createForm(MandateType::class, null, $this->getChoiceFormOptions());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Mandate $mandate */
            $mandate = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($mandate);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.mandate_created'));

            return $this->redirectToRoute('admin_mandate_edit', ['id' => $mandate->getId()]);
        }

        return $this->render('admin/page/mandate/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/{id}", name="admin_mandate_edit")
     * @return Response
     */
    public function editAction(Request $request, string $id, TranslatorInterface $translator)
    {
        $mandate = $this->getDoctrine()->getRepository('App:Mandate')->find($id);

        if (!$mandate) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(MandateType::class, $mandate, $this->getChoiceFormOptions());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Mandate $mandate */
            $mandate = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($mandate);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.mandate_updated'));

            return $this->redirectToRoute('admin_mandates');
        }

        return $this->render('admin/page/mandate/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/{id}/d", name="admin_mandate_delete", methods={"GET","POST"})
     */
    public function deleteAction(Request $request, string $id, TranslatorInterface $translator)
    {
        $mandate = $this->getDoctrine()->getRepository('App:Mandate')->find($id);
        if (!$mandate) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(MandateDeleteType::class, $mandate, [])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Mandate $mandate */
            $mandate = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->remove($mandate);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.mandate_deleted'));

            return $this->redirectToRoute('admin_mandates');
        }

        return $this->render('admin/page/mandate/delete.html.twig', [
            'form' => $form->createView(),
            'mandate' => $mandate,
        ]);
    }

    private function getChoiceFormOptions() : array
    {
        return [
            'elections' => $this->getDoctrine()->getRepository('App:Election')->getAdminChoices(),
            'constituencies' => $this->getDoctrine()->getRepository('App:Constituency')->getAdminChoices(),
            'politicians' => $this->getDoctrine()->getRepository('App:Politician')->getAdminChoices(),
            'institution_titles' => $this->getDoctrine()->getRepository('App:InstitutionTitle')->getAdminChoices(),
        ];
    }
}
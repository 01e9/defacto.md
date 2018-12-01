<?php


namespace App\Controller;

use App\Entity\Mandate;
use App\Form\MandateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/admin/mandates")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminMandatesController extends AbstractController
{
    /**
     * @Route(path="/add", name="admin_mandate_add")
     * @return Response
     */
    public function addAction(Request $request)
    {
        $form = $this->createForm(MandateType::class, null, [
            'politicians' => $this->getDoctrine()->getRepository('App:Politician')->getAdminChoices(),
            'institution_titles' => $this->getDoctrine()->getRepository('App:InstitutionTitle')->getAdminChoices(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Mandate $mandate */
            $mandate = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($mandate);
            $em->flush();

            $this->addFlash(
                'success',
                $this->get('translator')->trans('flash.mandate_created')
            );

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
    public function editAction(Request $request, string $id)
    {
        $mandate = $this->getDoctrine()->getRepository('App:Mandate')->find($id);

        if (!$mandate) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(MandateType::class, $mandate, [
            'politicians' => $this->getDoctrine()->getRepository('App:Politician')->getAdminChoices(),
            'institution_titles' => $this->getDoctrine()->getRepository('App:InstitutionTitle')->getAdminChoices(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Mandate $mandate */
            $mandate = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($mandate);
            $em->flush();

            $this->addFlash(
                'success',
                $this->get('translator')->trans('flash.mandate_updated')
            );

            return $this->redirectToRoute('admin_politicians');
        }

        return $this->render('admin/page/mandate/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
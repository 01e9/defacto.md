<?php


namespace App\Controller;

use App\Entity\Status;
use App\Form\StatusDeleteType;
use App\Form\StatusType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route(path="/admin/statuses")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminStatusesController extends AbstractController
{
    /**
     * @Route(path="/add", name="admin_status_add")
     * @return Response
     */
    public function addAction(Request $request, TranslatorInterface $translator)
    {
        $form = $this->createForm(StatusType::class, null, [])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Status $status */
            $status = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($status);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.status_created'));

            return $this->redirectToRoute('admin_status_edit', ['id' => $status->getId()]);
        }

        return $this->render('admin/page/status/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/{id}", name="admin_status_edit")
     * @return Response
     */
    public function editAction(Request $request, string $id, TranslatorInterface $translator)
    {
        $status = $this->getDoctrine()->getRepository('App:Status')->find($id);
        if (!$status) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(StatusType::class, $status, [])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Status $status */
            $status = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($status);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.status_updated'));

            return $this->redirectToRoute('admin_promises');
        }

        return $this->render('admin/page/status/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/{id}/d", name="admin_status_delete", methods={"GET","POST"})
     */
    public function deleteAction(Request $request, string $id, TranslatorInterface $translator)
    {
        $status = $this->getDoctrine()->getRepository('App:Status')->find($id);
        if (!$status) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(StatusDeleteType::class, $status, [])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Status $status */
            $status = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->remove($status);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.status_deleted'));

            return $this->redirectToRoute('admin_promises');
        }

        return $this->render('admin/page/status/delete.html.twig', [
            'form' => $form->createView(),
            'status' => $status,
        ]);
    }
}
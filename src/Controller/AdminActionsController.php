<?php

namespace App\Controller;

use App\Entity\Action;
use App\Form\ActionType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route(path="/admin")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminActionsController extends Controller
{
    /**
     * @Route(path="/actions", name="admin_actions")
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $actions = $this->getDoctrine()->getRepository('App:Action')->getAdminList($request);

        return $this->render('admin/page/action/index.html.twig', [
            'actions' => $actions,
        ]);
    }

    /**
     * @Route(path="/action/add", name="admin_action_add")
     * @return Response
     */
    public function addAction(Request $request)
    {
        $action = new Action();

        $form = $this->createForm(ActionType::class, $action, [
            'mandates' => $this->getDoctrine()->getRepository('App:Mandate')->getAdminChoices(),
            'actions' => [$action->getName() => $action],
            'promises' => $this->getDoctrine()->getRepository('App:Promise')->getAdminChoices(),
            'statuses' => $this->getDoctrine()->getRepository('App:Status')->getAdminChoices(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Action $action */
            $action = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($action);
            $em->flush();

            $this->addFlash(
                'success',
                $this->get('translator')->trans('flash.action_created')
            );

            return $this->redirectToRoute('admin_action_edit', ['id' => $action->getId()]);
        }

        return $this->render('admin/page/action/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/action/{id}", name="admin_action_edit")
     * @return Response
     */
    public function editAction(Request $request, string $id)
    {
        $action = $this->getDoctrine()->getRepository('App:Action')->find($id);
        if (!$action) {
            throw $this->createNotFoundException();
        }

        $originalStatusUpdates = new ArrayCollection();
        foreach ($action->getStatusUpdates() as $statusUpdate) {
            $originalStatusUpdates->add($statusUpdate);
        }

        $form = $this->createForm(ActionType::class, $action, [
            'mandates' => $this->getDoctrine()->getRepository('App:Mandate')->getAdminChoices(),
            'actions' => [$action->getName() => $action],
            'promises' => $this->getDoctrine()->getRepository('App:Promise')->getAdminChoices(),
            'statuses' => $this->getDoctrine()->getRepository('App:Status')->getAdminChoices(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Action $action */
            $action = $form->getData();

            $em = $this->getDoctrine()->getManager();

            foreach ($originalStatusUpdates as $statusUpdate) {
                if (false === $action->getStatusUpdates()->contains($statusUpdate)) {
                    $action->getStatusUpdates()->removeElement($statusUpdate);
                    $em->remove($statusUpdate);
                }
            }

            $em->persist($action);
            $em->flush();

            $this->addFlash(
                'success',
                $this->get('translator')->trans('flash.action_updated')
            );

            return $this->redirectToRoute('admin_actions');
        }

        return $this->render('admin/page/action/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
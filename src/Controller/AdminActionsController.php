<?php

namespace App\Controller;

use App\Entity\PromiseAction;
use App\Entity\PromiseUpdate;
use App\Form\ActionDeleteType;
use App\Form\ActionType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route(path="/admin/actions")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminActionsController extends AbstractController
{
    /**
     * @Route(path="/add", name="admin_action_add")
     * @return Response
     */
    public function addAction(Request $request, TranslatorInterface $translator)
    {
        $action = new PromiseAction();

        if (
            ($promise = $this->getDoctrine()->getRepository('App:Promise')->find(
                $request->query->get('promise', '~')
            ))
            &&
            ($mandate = $this->getDoctrine()->getRepository('App:Mandate')->findOneBy([
                'election' => $promise->getElection(),
                'politician' => $promise->getPolitician(),
            ]))
        ) {
            $action->setMandate($mandate);

            $mandates = [$mandate->getChoiceName() => $mandate];
            $promises = $powers = [];
        } else {
            $mandates = $this->getDoctrine()->getRepository('App:Mandate')->getAdminChoices();
            $promises = $powers = [];
        }

        $form = $this->createForm(ActionType::class, $action, [
            'mandates' => $mandates,
            'actions' => ['~' => $action],
            'promises' => $promises,
            'statuses' => $this->getDoctrine()->getRepository('App:Status')->getAdminChoices(),
            'powers' => $powers,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var PromiseAction $action */
            $action = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($action);

            if ($promise) {
                $action->setPromiseUpdates(new ArrayCollection([
                    (function () use (&$action, &$promise) {
                        $promiseUpdate = new PromiseUpdate();
                        $promiseUpdate->setAction($action);
                        $promiseUpdate->setPromise($promise);
                        return $promiseUpdate;
                    })()
                ]));
            }

            $em->flush();

            $this->addFlash('success', $translator->trans('flash.action_created'));

            return $this->redirectToRoute('admin_action_edit', ['id' => $action->getId()]);
        }

        return $this->render('admin/page/action/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/{id}", name="admin_action_edit")
     * @return Response
     */
    public function editAction(Request $request, string $id, TranslatorInterface $translator) {
        /** @var PromiseAction $action */
        $action = $this->getDoctrine()->getRepository('App:PromiseAction')->find($id);
        if (!$action) {
            throw $this->createNotFoundException();
        }

        $originalPromiseUpdates = new ArrayCollection();
        array_map([$originalPromiseUpdates, 'add'], $action->getPromiseUpdates()->toArray());

        $originalSources = new ArrayCollection();
        array_map([$originalSources, 'add'], $action->getSources()->toArray());

        $form = $this->createForm(ActionType::class, $action, [
            'mandates' => [$action->getMandate()->getChoiceName() => $action->getMandate()],
            'actions' => [$action->getName() => $action],
            'promises' => $this->getDoctrine()->getRepository('App:Promise')
                ->getAdminChoicesByMandate($action->getMandate()),
            'statuses' => $this->getDoctrine()->getRepository('App:Status')->getAdminChoices(),
            'powers' => $this->getDoctrine()->getRepository('App:PromiseAction')->getAdminPowerChoices($action),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var PromiseAction $action */
            $action = $form->getData();

            $em = $this->getDoctrine()->getManager();

            foreach ($originalPromiseUpdates as $promiseUpdate) {
                if (false === $action->getPromiseUpdates()->contains($promiseUpdate)) {
                    $action->getPromiseUpdates()->removeElement($promiseUpdate);
                    $em->remove($promiseUpdate);
                }
            }

            foreach ($originalSources as $source) {
                if (false === $action->getSources()->contains($source)) {
                    $action->getSources()->removeElement($source);
                    $em->remove($source);
                }
            }

            $em->persist($action);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.action_updated'));

            return $this->redirectToRoute('admin_action_edit', ['id' => $action->getId()]);
        }

        return $this->render('admin/page/action/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/{id}/d", name="admin_action_delete", methods={"GET","POST"})
     */
    public function deleteAction(Request $request, string $id, TranslatorInterface $translator)
    {
        $action = $this->getDoctrine()->getRepository('App:PromiseAction')->find($id);
        if (!$action) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(ActionDeleteType::class, $action, [])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var PromiseAction $action */
            $action = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->remove($action);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.action_deleted'));

            return $this->redirectToRoute('admin_promises');
        }

        return $this->render('admin/page/action/delete.html.twig', [
            'form' => $form->createView(),
            'action' => $action,
        ]);
    }
}
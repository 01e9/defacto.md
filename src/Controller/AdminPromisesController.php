<?php


namespace App\Controller;

use App\Entity\Promise;
use App\EventListener\DoctrineLogsListener;
use App\Form\PromiseType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/admin/promises")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminPromisesController extends Controller
{
    /**
     * @Route(path="", name="admin_promises")
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $promises = $this->getDoctrine()->getRepository('App:Promise')->getAdminList($request);
        $statuses = $this->getDoctrine()->getRepository('App:Status')->getAdminList($request);
        $orphanActions = $this->getDoctrine()->getRepository('App:Action')->getAdminOrphanList();

        return $this->render('admin/page/promise/index.html.twig', [
            'promises' => $promises,
            'statuses' => $statuses,
            'orphanActions' => $orphanActions,
        ]);
    }

    /**
     * @Route(path="/add", name="admin_promise_add")
     * @return Response
     */
    public function addAction(Request $request)
    {
        $promise = new Promise();

        $form = $this->createForm(PromiseType::class, $promise, [
            'categories' => $this->getDoctrine()->getRepository('App:Category')->getAdminChoices(),
            'mandates' => $this->getDoctrine()->getRepository('App:Mandate')->getAdminChoices(),
            'statuses' => $this->getDoctrine()->getRepository('App:Status')->getAdminChoices(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Promise $promise */
            $promise = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($promise);
            $em->flush();

            $this->addFlash(
                'success',
                $this->get('translator')->trans('flash.promise_created')
            );

            return $this->redirectToRoute('admin_promise_edit', ['id' => $promise->getId()]);
        }

        return $this->render('admin/page/promise/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/{id}", name="admin_promise_edit")
     * @return Response
     */
    public function editAction(Request $request, string $id)
    {
        $promise = $this->getDoctrine()->getRepository('App:Promise')->find($id);
        if (!$promise) {
            throw $this->createNotFoundException();
        }

        $originalSources = new ArrayCollection();
        foreach ($promise->getSources() as $source) {
            $originalSources->add($source);
        }

        if ($request->isMethod('POST')) {
            $this->get(DoctrineLogsListener::class)->addPromiseDataBefore($promise);
        }

        $actions = $this->getDoctrine()->getRepository('App:Action')->getAdminListByPromise($promise);

        $form = $this->createForm(PromiseType::class, $promise, [
            'categories' => $this->getDoctrine()->getRepository('App:Category')->getAdminChoices(),
            'mandates' => $this->getDoctrine()->getRepository('App:Mandate')->getAdminChoices(),
            'statuses' => $this->getDoctrine()->getRepository('App:Status')->getAdminChoices(),
            'promises' => [$promise->getName() => $promise],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Promise $promise */
            $promise = $form->getData();

            $em = $this->getDoctrine()->getManager();

            foreach ($originalSources as $source) {
                if (false === $promise->getSources()->contains($source)) {
                    $promise->getSources()->removeElement($source);
                    $em->remove($source);
                }
            }

            $em->persist($promise);
            $em->flush();

            $this->addFlash(
                'success',
                $this->get('translator')->trans('flash.promise_updated')
            );

            return $this->redirectToRoute('admin_promise_edit', ['id' => $promise->getId()]);
        }

        return $this->render('admin/page/promise/edit.html.twig', [
            'form' => $form->createView(),
            'actions' => $actions,
            'promise' => $promise,
            'logs' => $this->getDoctrine()->getRepository('App:Log')->findLatestByPromise($promise)
        ]);
    }
}
<?php

namespace App\Controller;

use App\Entity\Promise;
use App\Form\PromiseDeleteType;
use App\Form\PromisesFilterType;
use App\Form\PromiseType;
use App\Repository\PromiseActionRepository;
use App\Repository\PromiseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route(path="/admin/promises")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminPromisesController extends AbstractController
{
    private $promiseRepository;
    private $actionRepository;

    public function __construct(PromiseRepository $promiseRepository, PromiseActionRepository $actionRepository)
    {
        $this->promiseRepository = $promiseRepository;
        $this->actionRepository = $actionRepository;
    }

    /**
     * @Route(path="", name="admin_promises")
     */
    public function indexAction(Request $request): Response
    {
        $filterForm = $this->createForm(PromisesFilterType::class);
        $filterForm->handleRequest($request);
        $filterData = ($filterForm->isSubmitted() && $filterForm->isValid()) ? $filterForm->getData() : null;

        $pagination = $this->promiseRepository->getAdminListPaginated($request, $filterData);

        return $this->render('admin/page/promise/index.html.twig', [
            'pagination' => $pagination,
            'orphanActions' => $this->actionRepository->getAdminOrphanList(),
            'filterForm' => $filterForm->createView(),
        ]);
    }

    /**
     * @Route(path="/add", name="admin_promise_add")
     * @return Response
     */
    public function addAction(Request $request, TranslatorInterface $translator)
    {
        $promise = new Promise();

        $form = $this->createForm(PromiseType::class, $promise, [
            'categories' => $this->getDoctrine()->getRepository('App:PromiseCategory')->getAdminChoices(),
            'elections' => $this->getDoctrine()->getRepository('App:Election')->getAdminChoices(),
            'politicians' => $this->getDoctrine()->getRepository('App:Politician')->getAdminChoices(),
            'statuses' => $this->getDoctrine()->getRepository('App:Status')->getAdminChoices(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Promise $promise */
            $promise = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($promise);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.promise_created'));

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
    public function editAction(Request $request, string $id, TranslatorInterface $translator)
    {
        $promise = $this->getDoctrine()->getRepository('App:Promise')->find($id);
        if (!$promise) {
            throw $this->createNotFoundException();
        }

        $originalSources = new ArrayCollection();
        array_map([$originalSources, 'add'], $promise->getSources()->toArray());

        $actions = $this->getDoctrine()->getRepository('App:PromiseAction')->getAdminListByPromise($promise);

        $form = $this->createForm(PromiseType::class, $promise, [
            'categories' => $this->getDoctrine()->getRepository('App:PromiseCategory')->getAdminChoices(),
            'elections' => $this->getDoctrine()->getRepository('App:Election')->getAdminChoices(),
            'politicians' => $this->getDoctrine()->getRepository('App:Politician')->getAdminChoices(),
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

            $this->addFlash('success', $translator->trans('flash.promise_updated'));

            return $this->redirectToRoute('admin_promise_edit', ['id' => $promise->getId()]);
        }

        return $this->render('admin/page/promise/edit.html.twig', [
            'form' => $form->createView(),
            'actions' => $actions,
            'promise' => $promise,
        ]);
    }

    /**
     * @Route(path="/{id}/d", name="admin_promise_delete", methods={"GET","POST"})
     */
    public function deleteAction(Request $request, string $id, TranslatorInterface $translator)
    {
        $promise = $this->getDoctrine()->getRepository('App:Promise')->find($id);
        if (!$promise) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(PromiseDeleteType::class, $promise, [])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Promise $promise */
            $promise = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->remove($promise);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.promise_deleted'));

            return $this->redirectToRoute('admin_promises');
        }

        return $this->render('admin/page/promise/delete.html.twig', [
            'form' => $form->createView(),
            'promise' => $promise,
        ]);
    }
}
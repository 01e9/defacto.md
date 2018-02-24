<?php


namespace App\Controller;

use App\Entity\Promise;
use App\Form\PromiseType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/admin")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminPromisesController extends Controller
{
    /**
     * @Route(path="/promises", name="admin_promises")
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $promises = $this->getDoctrine()->getRepository('App:Promise')->getAdminList($request);
        $statuses = $this->getDoctrine()->getRepository('App:Status')->getAdminList($request);

        return $this->render('admin/page/promise/index.html.twig', [
            'promises' => $promises,
            'statuses' => $statuses,
        ]);
    }

    /**
     * @Route(path="/promise/add", name="admin_promise_add")
     * @return Response
     */
    public function addAction(Request $request)
    {
        $form = $this->createForm(PromiseType::class, null, [
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

            return $this->redirectToRoute('admin_promises');
        }

        return $this->render('admin/page/promise/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/promise/{id}", name="admin_promise_edit")
     * @return Response
     */
    public function editAction(Request $request, string $id)
    {
        $promise = $this->getDoctrine()->getRepository('App:Promise')->find($id);
        if (!$promise) {
            throw $this->createNotFoundException();
        }

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
                $this->get('translator')->trans('flash.promise_updated')
            );

            return $this->redirectToRoute('admin_promises');
        }

        return $this->render('admin/page/promise/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
<?php


namespace App\Controller;

use App\Entity\Politician;
use App\Form\PoliticianDeleteType;
use App\Form\PoliticianType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route(path="/admin/politicians")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminPoliticiansController extends AbstractController
{
    /**
     * @Route(path="", name="admin_politicians")
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $politicians = $this->getDoctrine()->getRepository('App:Politician')->getAdminList($request);
        $mandates = $this->getDoctrine()->getRepository('App:Mandate')->getAdminList($request);

        return $this->render('admin/page/politician/index.html.twig', [
            'politicians' => $politicians,
            'mandates' => $mandates,
        ]);
    }

    /**
     * @Route(path="/add", name="admin_politician_add")
     * @return Response
     */
    public function addAction(Request $request, TranslatorInterface $translator)
    {
        $form = $this->createForm(PoliticianType::class, null, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Politician $politician */
            $politician = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($politician);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.politician_created'));

            return $this->redirectToRoute('admin_politician_edit', ['id' => $politician->getId()]);
        }

        return $this->render('admin/page/politician/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/{id}", name="admin_politician_edit")
     * @return Response
     */
    public function editAction(Request $request, string $id, TranslatorInterface $translator)
    {
        $politician = $this->getDoctrine()->getRepository('App:Politician')->find($id);

        if (!$politician) {
            throw $this->createNotFoundException();
        }

        /** @var File $initialPhoto */
        $initialPhoto = $politician->getPhoto();

        $form = $this->createForm(PoliticianType::class, $politician, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Politician $politician */
            $politician = $form->getData();

            if (!$politician->getPhoto()) {
                $politician->setPhoto($initialPhoto);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($politician);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.politician_updated'));

            return $this->redirectToRoute('admin_politicians');
        }

        return $this->render('admin/page/politician/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/{id}/d", name="admin_politician_delete", methods={"GET","POST"})
     */
    public function deleteAction(Request $request, string $id, TranslatorInterface $translator)
    {
        $politician = $this->getDoctrine()->getRepository('App:Politician')->find($id);
        if (!$politician) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(PoliticianDeleteType::class, $politician, [])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Politician $politician */
            $politician = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->remove($politician);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.politician_deleted'));

            return $this->redirectToRoute('admin_politicians');
        }

        return $this->render('admin/page/politician/delete.html.twig', [
            'form' => $form->createView(),
            'politician' => $politician,
        ]);
    }
}
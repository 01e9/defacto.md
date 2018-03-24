<?php

namespace App\Controller;

use App\Entity\Status;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class StatusesController extends AbstractController
{
    /**
     * @Route(path="/s/{slug}", name="status")
     * @Method("GET")
     */
    public function viewAction(Request $request, string $slug)
    {
        /** @var Status $status */
        $status = $this->getDoctrine()->getRepository('App:Status')->findOneBy([
            'slug' => $slug,
        ]);

        if (!$status) {
            throw $this->createNotFoundException();
        }

        return $this->render('app/page/status.html.twig', [
            'status' => $status,
        ]);
    }
}
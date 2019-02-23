<?php

namespace App\Controller;

use App\Entity\Status;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class StatusesController extends AbstractController
{
    /**
     * @Route(path="/st/{slug}", name="status", methods={"GET"})
     */
    public function viewAction(Request $request, string $slug)
    {
        $status = null;

        if ('~' !== $slug) {
            /** @var Status $status */
            $status = $this->getDoctrine()->getRepository('App:Status')->findOneBy([
                'slug' => $slug,
            ]);

            if (!$status) {
                throw $this->createNotFoundException();
            }
        }

        return $this->render('app/page/status.html.twig', [
            'status' => $status,
        ]);
    }
}
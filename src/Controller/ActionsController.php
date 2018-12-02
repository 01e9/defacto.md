<?php

namespace App\Controller;

use App\Entity\Action;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActionsController extends AbstractController
{
    /**
     * @Route(path="/a/{id}", name="action", methods={"GET"})
     */
    public function viewAction(Request $request, string $id)
    {
        /** @var Action $action */
        $action = $this->getDoctrine()->getRepository('App:Action')->findOneBy([
            'id' => $id,
            'published' => true,
        ]);

        if (!$action) {
            throw $this->createNotFoundException();
        }

        return new Response('Pagina acțiunei? Slug?');
    }
}
<?php

namespace App\Controller;

use App\Entity\Promise;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PromisesController extends AbstractController
{
    /**
     * @Route(path="/pr/{slug}", name="promise", methods={"GET"})
     */
    public function viewAction(Request $request, string $slug)
    {
        /** @var Promise $promise */
        $promise = $this->getDoctrine()->getRepository('App:Promise')->findOneBy([
            'slug' => $slug,
            'published' => true,
        ]);

        if (!$promise) {
            throw $this->createNotFoundException();
        }

        return $this->render('app/page/promise.html.twig', [
            'promise' => $promise,
        ]);
    }
}
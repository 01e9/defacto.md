<?php

namespace App\Controller;

use App\Entity\Politician;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PoliticiansController extends AbstractController
{
    /**
     * @Route("/po/{slug}", name="politician")
     */
    public function viewAction(string $slug)
    {
        /** @var Politician $politician */
        $politician = $this->getDoctrine()->getRepository('App:Politician')->findOneBy([
            'slug' => $slug,
        ]);
        if (!$politician) {
            throw $this->createNotFoundException();
        }

        return $this->render('app/page/politician.html.twig', [
            'politician' => $politician,
        ]);
    }
}

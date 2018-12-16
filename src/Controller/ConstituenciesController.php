<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ConstituenciesController extends AbstractController
{
    /**
     * @Route("/constituency", name="constituency")
     */
    public function index()
    {
        return $this->render('app/page/constituency.html.twig', [
            'controller_name' => 'ConstituenciesController',
        ]);
    }
}

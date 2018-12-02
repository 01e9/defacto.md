<?php

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login", methods={"GET","POST"})
     */
    public function login(Request $request, AuthenticationUtils $authUtils, TranslatorInterface $translator)
    {
        if ($this->isGranted('ROLE_USER')) {
            $this->addFlash('warning', $translator->trans('flash.already_logged_in'));

            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(LoginType::class, [
            '_target_path' => $this->generateUrl('home')
        ]);
        $form->handleRequest($request);

        return $this->render('app/page/login.html.twig', [
            'form' => $form->createView(),
            'error' => $authUtils->getLastAuthenticationError(),
            'last_username' => $authUtils->getLastUsername(),
        ]);
    }
}
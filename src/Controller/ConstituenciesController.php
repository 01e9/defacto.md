<?php

namespace App\Controller;

use App\Entity\Constituency;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ConstituenciesController extends AbstractController
{
    /**
     * @Route("/c/{slug}", name="constituency")
     */
    public function viewAction(string $slug)
    {
        /** @var Constituency $constituency */
        $constituency = $this->getDoctrine()->getRepository('App:Constituency')->findOneBy([
            'slug' => $slug,
        ]);
        if (!$constituency) {
            throw $this->createNotFoundException();
        }

        $elections = [];
        foreach ($constituency->getCandidates() as $candidate) {
            $election = $candidate->getElection();
            $elections[ $election->getId() ]['election'] = $election;
            $elections[ $election->getId() ]['candidates'][] = $candidate;
        }
        foreach ($constituency->getProblems() as $problem) {
            $election = $problem->getElection();
            $elections[ $election->getId() ]['election'] = $election;
            $elections[ $election->getId() ]['problems'][] = $problem;
        }

        return $this->render('app/page/constituency.html.twig', [
            'constituency' => $constituency,
            'elections' => $elections,
        ]);
    }
}

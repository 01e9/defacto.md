<?php

namespace App\Controller;

use App\Entity\Constituency;
use App\Entity\Election;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ConstituenciesController extends AbstractController
{
    /**
     * @Route("/c/{slug}/{electionSlug}", name="constituency")
     */
    public function viewAction(string $slug, string $electionSlug)
    {
        /** @var Constituency $constituency */
        $constituency = $this->getDoctrine()->getRepository('App:Constituency')->findOneBy([
            'slug' => $slug,
        ]);
        if (!$constituency) {
            throw $this->createNotFoundException();
        }

        /** @var Election $election */
        $election = $this->getDoctrine()->getRepository('App:Election')->findOneBy([
            'slug' => $electionSlug,
        ]);
        if (!$election) {
            throw $this->createNotFoundException();
        }

        $elections = [];
        foreach ($constituency->getCandidates() as $candidate) {
            $el = $candidate->getElection();
            $elections[ $el->getId() ]['election'] = $el;
            $elections[ $el->getId() ]['candidates'][] = $candidate;
        }
        foreach ($constituency->getProblems() as $problem) {
            $el = $problem->getElection();
            $elections[ $el->getId() ]['election'] = $el;
            $elections[ $el->getId() ]['problems'][] = $problem;
        }
        foreach ($constituency->getCandidateProblemOpinions() as $opinion) {
            $el = $opinion->getElection();
            $elections[ $el->getId() ]['election'] = $el;
            $elections[ $el->getId() ]['problemOpinions'][ $opinion->getProblem()->getId() ][] = $opinion;
        }

        return $this->render('app/page/constituency.html.twig', [
            'constituency' => $constituency,
            'election' => $elections[ $election->getId() ],
        ]);
    }
}

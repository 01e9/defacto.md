<?php

namespace App\Controller;

use App\Entity\Constituency;
use App\Entity\Election;
use App\Entity\Mandate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ConstituenciesController extends AbstractController
{
    /**
     * @Route("/constituency/{slug}/{electionSlug}", name="constituency")
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

        $mandates = [];
        foreach (
            $this->getDoctrine()->getRepository('App:Mandate')->findBy([
                'constituency' => $constituency,
                'election' => $election,
            ])
            /** @var Mandate $mandate */
            as $mandate
        ) {
            $mandates[ $mandate->getPolitician()->getId() ] = $mandate;
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

        if (empty($elections[ $election->getId() ])) {
            throw $this->createNotFoundException();
        }

        return $this->render('app/page/constituency.html.twig', [
            'constituency' => $constituency,
            'election' => $election,
            'elections' => $elections,
            'mandates' => $mandates,
        ]);
    }
}

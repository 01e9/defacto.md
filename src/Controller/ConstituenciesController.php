<?php

namespace App\Controller;

use App\Entity\Constituency;
use App\Entity\Election;
use App\Entity\Mandate;
use App\Repository\ConstituencyRepository;
use App\Repository\ElectionRepository;
use App\Repository\MandateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ConstituenciesController extends AbstractController
{
    private ConstituencyRepository $repository;

    public function __construct(ConstituencyRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/constituency/{slug}/{electionSlug}", name="constituency")
     */
    public function viewAction(
        string $slug, string $electionSlug,
        ElectionRepository $electionRepository,
        MandateRepository $mandateRepository
    )
    {
        /** @var Constituency $constituency */
        $constituency = $this->repository->findOneBy(['slug' => $slug]);
        if (!$constituency) {
            throw $this->createNotFoundException();
        }

        /** @var Election $election */
        $election = $electionRepository->findOneBy(['slug' => $electionSlug]);
        if (!$election) {
            throw $this->createNotFoundException();
        }

        $politicianToMandate = [];
        $mandatesCompetencePointsRanks = [];
        foreach (
            $mandateRepository->findBy(['constituency' => $constituency, 'election' => $election])
            as $mandate /** @var Mandate $mandate */
        ) {
            $politicianToMandate[ $mandate->getPolitician()->getId() ] = $mandate;
            $mandatesCompetencePointsRanks[ $mandate->getId() ] = $mandateRepository->findCompetencePointsRank($mandate);
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
            'politician_to_mandate' => $politicianToMandate,
            'mandates_competence_points_ranks' => $mandatesCompetencePointsRanks,
        ]);
    }
}

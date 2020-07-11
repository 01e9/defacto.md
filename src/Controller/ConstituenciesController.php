<?php

namespace App\Controller;

use App\Entity\Constituency;
use App\Entity\Election;
use App\Entity\Mandate;
use App\Entity\Candidate;
use App\Entity\CandidateProblemOpinion;
use App\Repository\CandidateProblemOpinionRepository;
use App\Repository\CandidateRepository;
use App\Repository\ConstituencyProblemRepository;
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
     * @Route("/constituency/{slug}/{electionSlug}", name="constituency_election")
     */
    public function viewElectionAction(
        string $slug, string $electionSlug,
        ElectionRepository $electionRepository,
        MandateRepository $mandateRepository,
        CandidateRepository $candidateRepository,
        ConstituencyProblemRepository $constituencyProblemRepository,
        CandidateProblemOpinionRepository $problemOpinionRepository
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

        $mandates = $mandateRanks = [];
        foreach (
            $mandateRepository->findBy(['election' => $election, 'constituency' => $constituency])
            as $mandate /** @var Mandate $mandate */
        ) {
            $mandates[ $mandate->getPolitician()->getId() ] = $mandate;
            $mandateRanks[ $mandate->getId() ] = $mandate->getElection()->isCompetenceUseTracked()
                ? $mandateRepository->findCompetencePointsRank($mandate)
                : 0;
        }

        $candidates = [];
        foreach (
            $candidateRepository->findBy(
                ['election' => $election, 'constituency' => $constituency],
                ['registrationDate' => 'DESC']
            )
            as $candidate /** @var Candidate $candidate */
        ) {
            $candidates[ $candidate->getPolitician()->getId() ] = $candidate;
        }

        $problems = $constituencyProblemRepository->findBy(
            ['election' => $election, 'constituency' => $constituency],
            ['percentage' => 'DESC']
        );

        $problemOpinions = [];
        foreach (
            $problemOpinionRepository->findBy(['election' => $election, 'constituency' => $constituency])
            as $problemOpinion /** @var CandidateProblemOpinion $problemOpinion */
        ) {
            $problemOpinions[ $problemOpinion->getProblem()->getId() ][] = $problemOpinion;
        }

        $electionData = $electionRepository->getElectionData($election);

        return $this->render('app/page/constituency-election.html.twig', [
            'constituency' => $constituency,
            'election' => $election,
            'mandates' => $mandates,
            'mandate_ranks' => $mandateRanks,
            'candidates' => $candidates,
            'problems' => $problems,
            'problem_opinions' => $problemOpinions,
            'election_data' => $electionData,
        ]);
    }
}

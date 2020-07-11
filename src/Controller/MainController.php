<?php

namespace App\Controller;

use App\Repository\BlogPostRepository;
use App\Repository\ElectionRepository;
use App\Repository\MandateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    public function redirectToLocaleAction()
    {
        return $this->redirectToRoute('home');
    }

    /**
     * @Route(path="", name="home", methods={"GET"})
     */
    public function homeAction(
        MandateRepository $mandateRepository,
        ElectionRepository $electionRepository,
        BlogPostRepository $blogPostRepository
    )
    {
        $currentElectionData = ($currentElection = $electionRepository->getCurrentElection())
            ? $electionRepository->getElectionData($currentElection)
            : [];

        return $this->render('app/page/home.html.twig', [
            'president_mandate' => $mandateRepository->getCurrentPresidentStatistics(),
            'current_election' => $currentElectionData,
            'top_rank_mandates' => ($currentElectionData && $currentElectionData->election)
                ? $mandateRepository->getTopRanked($currentElectionData->election)
                : null,
            'latest_posts' => $blogPostRepository->getRecentPublicPosts(),
        ]);
    }
}
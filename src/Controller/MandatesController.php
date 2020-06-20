<?php

namespace App\Controller;

use App\Entity\Mandate;
use App\Repository\ElectionRepository;
use App\Repository\MandateRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MandatesController extends AbstractController
{
    private PaginatorInterface $paginator;
    private MandateRepository $repository;

    public function __construct(PaginatorInterface $paginator, MandateRepository $repository)
    {
        $this->paginator = $paginator;
        $this->repository = $repository;
    }

    /**
     * @Route(
     *     path="/mandates/{electionSlug}",
     *     name="mandates",
     *     methods={"GET"}
     * )
     */
    public function indexAction(
        Request $request, string $electionSlug,
        ElectionRepository $electionRepository
    )
    {
        $election = $electionRepository->findOneBy(['slug' => $electionSlug]);
        if (!$election || !$election->isCompetenceUseTracked()) {
            throw $this->createNotFoundException();
        }

        $query = $this->repository->createQueryBuilder('m')
            ->where('m.ceasingDate IS NULL')
            ->andWhere('m.election IN (:electionIds)')
            ->orderBy('m.competenceUsesPoints', 'DESC')
            ->setParameter('electionIds', $electionRepository->findWithSubElectionsIds($election));

        $mandates = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1), // fixme: hardcode
            20 // fixme: hardcode
        );

        $firstMandateRank = ($firstMandate = $mandates->getItems()[0] ?? null)
            ? $this->repository->findCompetencePointsRank($firstMandate)
            : 0;

        return $this->render('app/page/mandates.html.twig', [
            'election' => $election,
            'mandates' => $mandates,
            'first_mandate_rank' => $firstMandateRank,
        ]);
    }

    /**
     * @Route("/mandate/{electionSlug}/{politicianSlug}", name="mandate", methods={"GET"})
     */
    public function viewAction(string $electionSlug, string $politicianSlug)
    {
        $mandate = $this->repository->findOneBySlugs($electionSlug, $politicianSlug);
        if (!$mandate) {
            throw $this->createNotFoundException();
        }

        $rank = $this->repository->findCompetencePointsRank($mandate);

        return $this->render('app/page/mandate.html.twig', [
            'mandate' => $mandate,
            'rank' => $rank,
        ]);
    }
}
<?php

namespace App\Controller;

use App\Consts;
use App\Filter\MandateFilter;
use App\Form\Filter\MandateFilterType;
use App\Repository\CompetenceCategoryRepository;
use App\Repository\CompetenceUseRepository;
use App\Repository\ElectionRepository;
use App\Repository\MandateCompetenceCategoryStatsRepository;
use App\Repository\MandateRepository;
use App\Service\MandateCompetenceUseStatsComputer;
use App\Validator\MandateQueryFilters;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

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
            ->andWhere('m.election IN (:elections)')
            ->orderBy('m.competenceUsesPoints', 'DESC')
            ->setParameter('elections', $electionRepository->findWithSubElections($election));

        $mandates = $this->paginator->paginate(
            $query,
            $request->query->getInt(Consts::QUERY_PARAM_PAGE, 1),
            Consts::PAGINATION_SIZE_MANDATES
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
    public function viewAction(
        string $electionSlug, string $politicianSlug,
        MandateCompetenceCategoryStatsRepository $categoryStatsRepository,
        CompetenceUseRepository $competenceUseRepository,
        ValidatorInterface $validator,
        Request $request,
        CompetenceCategoryRepository $categoryRepository,
        MandateCompetenceUseStatsComputer $competenceUseStatsComputer
    )
    {
        $filterViolations = $validator->validate($request->query->all(), new MandateQueryFilters());
        if (!($filterIsValid = !$filterViolations->count())) {
            $filter = new MandateFilter();

            foreach ($filterViolations as $violation) {
                /** @var ConstraintViolationInterface $violation */
                $message = "{$violation->getPropertyPath()}: {$violation->getMessage()}";
                $this->addFlash('error', $message);
            }
        } else {
            $filter = MandateFilter::createFromValidQueryArray($request->query->all(), $categoryRepository);
        }

        $mandate = $this->repository->findOneBySlugs($electionSlug, $politicianSlug);
        if (!$mandate) {
            throw $this->createNotFoundException();
        }

        $filterForm = $this->createForm(MandateFilterType::class);
        if ($filterIsValid) {
            $filterForm->handleRequest($request);
        }

        $rank = $this->repository->findCompetencePointsRank($mandate);
        $categoryStatsByParent = $categoryStatsRepository->findStatsByParentCategory(
            $mandate,
            $filter->isEmpty() ? null : $competenceUseStatsComputer->compute($mandate, $filter)->categoryStats
        );
        $statsByMonth = $competenceUseRepository->findUseCountByMonth($mandate);
        $competenceUses = $competenceUseRepository->findByFilter($mandate, $filter);

        return $this->render('app/page/mandate.html.twig', [
            'mandate' => $mandate,
            'rank' => $rank,
            'categoryStatsByParent' => $categoryStatsByParent,
            'statsByMonth' => $statsByMonth,
            'competenceUses' => $competenceUses,
            'filter' => $filter,
            'filterForm' => $filterForm->createView(),
        ]);
    }
}
<?php

namespace App\Controller;

use App\Entity\Election;
use App\Entity\Politician;
use App\Entity\Promise;
use App\Entity\Status;
use App\Repository\ElectionRepository;
use App\Repository\PoliticianRepository;
use App\Repository\PromiseRepository;
use App\Repository\StatusRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class StatusesController extends AbstractController
{
    private $paginator;
    private $statusRepository;
    private $promiseRepository;
    private $politicianRepository;
    private $electionRepository;

    public function __construct(
        PaginatorInterface $paginator,
        StatusRepository $statusRepository,
        PromiseRepository $promiseRepository,
        PoliticianRepository $politicianRepository,
        ElectionRepository $electionRepository
    )
    {
        $this->paginator = $paginator;
        $this->statusRepository = $statusRepository;
        $this->promiseRepository = $promiseRepository;
        $this->politicianRepository = $politicianRepository;
        $this->electionRepository = $electionRepository;
    }

    /**
     * @Route(
     *     path="/st/{slug}/{politicianSlug}/{electionSlug}",
     *     name="status",
     *     methods={"GET"},
     *     defaults={"slug"="*", "politicianSlug"="*", "electionSlug"="*"}
     * )
     */
    public function viewAction(Request $request, string $slug, string $politicianSlug, string $electionSlug)
    {
        $promisesQuery = $this->promiseRepository->createQueryBuilder('p')->orderBy('p.madeTime', 'DESC');
        $promisesQuery->andWhere('p.published = :published')->setParameter('published', true);

        switch ($slug) {
            case '~':
                $status = null;
                $promisesQuery->andWhere('p.status IS NULL');
                break;
            case '*':
                $status = false;
                break;
            default:
                $status = $this->statusRepository->findOneBy(['slug' => $slug]); /** @var Status $status */
                if (!$status) {
                    throw $this->createNotFoundException();
                }
                $promisesQuery->andWhere('p.status = :status')->setParameter('status', $status);
        }

        $politician = null;
        if ($politicianSlug !== '*') {
            $politician = $this->politicianRepository->findOneBy(['slug' => $politicianSlug]); /** @var Politician $politician */
            if (!$politician) {
                throw $this->createNotFoundException();
            }
            $promisesQuery->andWhere('p.politician = :politician')->setParameter('politician', $politician);
        }

        $election = null;
        if ($electionSlug !== '*') {
            $election = $this->electionRepository->findOneBy(['slug' => $electionSlug]); /** @var Election $election */
            if (!$election) {
                throw $this->createNotFoundException();
            }
            $promisesQuery->andWhere('p.election = :election')->setParameter('election', $election);
        }

        $promises = $this->paginator->paginate(
            $promisesQuery,
            $request->query->getInt('page', 1), // fixme: hardcode
            9 // fixme: hardcode
        );

        return $this->render('app/page/status.html.twig', [
            'status' => $status,
            'promises' => $promises,
            'politician' => $politician,
            'election' => $election,
        ]);
    }
}
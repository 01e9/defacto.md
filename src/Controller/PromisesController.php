<?php

namespace App\Controller;

use App\Entity\Election;
use App\Entity\Mandate;
use App\Entity\Politician;
use App\Entity\Promise;
use App\Entity\Status;
use App\Repository\ElectionRepository;
use App\Repository\MandateRepository;
use App\Repository\PoliticianRepository;
use App\Repository\PromiseRepository;
use App\Repository\StatusRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PromisesController extends AbstractController
{
    private $paginator;
    private $promiseRepository;
    private $mandateRepository;
    private $statusRepository;
    private $politicianRepository;
    private $electionRepository;

    public function __construct(
        PaginatorInterface $paginator,
        StatusRepository $statusRepository,
        PromiseRepository $promiseRepository,
        MandateRepository $mandateRepository,
        PoliticianRepository $politicianRepository,
        ElectionRepository $electionRepository
    )
    {
        $this->paginator = $paginator;
        $this->statusRepository = $statusRepository;
        $this->politicianRepository = $politicianRepository;
        $this->electionRepository = $electionRepository;
        $this->promiseRepository = $promiseRepository;
        $this->mandateRepository = $mandateRepository;
    }

    /**
     * @Route(
     *     path="/promises/{statusSlug}/{politicianSlug}/{electionSlug}",
     *     name="promises",
     *     methods={"GET"},
     *     defaults={"statusSlug"="*", "politicianSlug"="*", "electionSlug"="*"}
     * )
     */
    public function indexAction(Request $request, string $statusSlug, string $politicianSlug, string $electionSlug)
    {
        $promisesQuery = $this->promiseRepository->createQueryBuilder('p')->orderBy('p.madeTime', 'DESC');
        $promisesQuery->andWhere('p.published = :published')->setParameter('published', true);

        switch ($statusSlug) {
            case '~':
                $status = null;
                $promisesQuery->andWhere('p.status IS NULL');
                break;
            case '*':
                $status = false;
                break;
            default:
                $status = $this->statusRepository->findOneBy(['slug' => $statusSlug]); /** @var Status $status */
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

        return $this->render('app/page/promises.html.twig', [
            'promises' => $promises,
            'status' => $status,
            'politician' => $politician,
            'election' => $election,
        ]);
    }

    /**
     * @Route(path="/promise/{slug}", name="promise", methods={"GET"})
     */
    public function viewAction(Request $request, string $slug)
    {
        /** @var Promise $promise */
        $promise = $this->promiseRepository->findOneBy(['published' => true, 'slug' => $slug]);
        if (!$promise) {
            throw $this->createNotFoundException();
        }

        /** @var Mandate $mandate */
        $mandate = $this->mandateRepository->findOneBy([
            'politician' => $promise->getPolitician(),
            'election' => $promise->getElection(),
        ]);

        return $this->render('app/page/promise.html.twig', [
            'promise' => $promise,
            'mandate' => $mandate,
        ]);
    }
}
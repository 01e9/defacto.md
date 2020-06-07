<?php

namespace App\Controller;

use App\Entity\Mandate;
use App\Entity\Politician;
use App\Entity\Promise;
use App\Repository\MandateRepository;
use App\Repository\PoliticianRepository;
use App\Repository\PromiseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PoliticiansController extends AbstractController
{
    private PoliticianRepository $politicianRepository;
    private PromiseRepository $promiseRepository;
    private MandateRepository $mandateRepository;

    public function __construct(
        PoliticianRepository $politicianRepository,
        PromiseRepository $promiseRepository,
        MandateRepository $mandateRepository
    )
    {
        $this->politicianRepository = $politicianRepository;
        $this->promiseRepository = $promiseRepository;
        $this->mandateRepository = $mandateRepository;
    }

    /**
     * @Route("/politician/{slug}", name="politician")
     */
    public function viewAction(string $slug)
    {
        /** @var Politician $politician */
        $politician = $this->politicianRepository->findOneBy(['slug' => $slug]);
        if (!$politician) {
            throw $this->createNotFoundException();
        }

        $lastValidMandate = null;

        $mandatesByElection = [];
        foreach ($politician->getMandates()->toArray() as $mandate) { /** @var Mandate $mandate */
            $mandatesByElection[ $mandate->getElection()->getId() ] = $mandate;

            if (!$lastValidMandate && !$mandate->getCeasingDate()) {
                $lastValidMandate = $mandate;
            }
        }

        $lastValidMandate = $lastValidMandate ? [
            'mandate' => $lastValidMandate,
            'competencePointsRank' => $this->mandateRepository->findCompetencePointsRank($lastValidMandate),
        ] : null;

        $promisesByElection = [];
        foreach ($this->promiseRepository->findBy(['politician' => $politician, 'published' => true]) as $promise) { /** @var Promise $promise */
            $promisesByElection[ $promise->getElection()->getId() ][] = $promise;
        }
        foreach ($promisesByElection as $electionId => $promises) {
            uasort($promises, function(Promise $a, Promise $b) {
                return ($a->getMadeTime() === $b->getMadeTime()) ? 0 : ($a->getMadeTime() > $b->getMadeTime() ? -1 : 1);
            });
            $promisesByElection[$electionId] = $promises;
        }

        return $this->render('app/page/politician.html.twig', [
            'politician' => $politician,
            'mandates_by_election' => $mandatesByElection,
            'promises_by_election' => $promisesByElection,
            'last_valid_mandate' => $lastValidMandate,
        ]);
    }
}

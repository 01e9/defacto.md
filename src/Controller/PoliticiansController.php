<?php

namespace App\Controller;

use App\Entity\Mandate;
use App\Entity\Politician;
use App\Entity\Promise;
use App\Repository\PoliticianRepository;
use App\Repository\PromiseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PoliticiansController extends AbstractController
{
    private $politicianRepository;
    private $promiseRepository;

    public function __construct(PoliticianRepository $politicianRepository, PromiseRepository $promiseRepository)
    {
        $this->politicianRepository = $politicianRepository;
        $this->promiseRepository = $promiseRepository;
    }

    /**
     * @Route("/po/{slug}", name="politician")
     */
    public function viewAction(string $slug)
    {
        /** @var Politician $politician */
        $politician = $this->politicianRepository->findOneBy(['slug' => $slug]);
        if (!$politician) {
            throw $this->createNotFoundException();
        }

        $mandatesByElection = [];
        foreach ($politician->getMandates()->toArray() as $mandate) { /** @var Mandate $mandate */
            $mandatesByElection[ $mandate->getElection()->getId() ] = $mandate;
        }

        $promisesByElection = [];
        foreach ($this->promiseRepository->findBy(['politician' => $politician]) as $promise) { /** @var Promise $promise */
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
            'candidates' => $politician->getSortedCandidates(),
            'mandatesByElection' => $mandatesByElection,
            'promisesByElection' => $promisesByElection,
        ]);
    }
}

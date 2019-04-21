<?php

namespace App\Controller;

use App\Entity\Mandate;
use App\Entity\Promise;
use App\Repository\MandateRepository;
use App\Repository\PromiseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PromisesController extends AbstractController
{
    private $promiseRepository;
    private $mandateRepository;

    public function __construct(
        PromiseRepository $promiseRepository,
        MandateRepository $mandateRepository
    )
    {
        $this->promiseRepository = $promiseRepository;
        $this->mandateRepository = $mandateRepository;
    }

    /**
     * @Route(path="/pr/{slug}", name="promise", methods={"GET"})
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
<?php

namespace App\Controller;

use App\Entity\InstitutionTitle;
use App\Entity\Mandate;
use App\Entity\Promise;
use App\Repository\SettingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function homeAction(Request $request)
    {
        return $this->render('app/page/home.html.twig', [
            'president_mandate' => $this->getPresidentMandateData(),
        ]);
    }

    private function getPresidentMandateData()
    {
        /** @var InstitutionTitle $institutionTitle */
        $institutionTitle = $this->getDoctrine()->getRepository('App:Setting')
            ->get(SettingRepository::PRESIDENT_INSTITUTION_TITLE_ID);
        /** @var Mandate $mandate */
        $mandate = $this->getDoctrine()->getRepository('App:Mandate')
            ->getLatestByInstitutionTitle($institutionTitle);

        if (!$mandate) {
            throw new \Exception('President mandate is required');
        }

        $powersStatistics = $this->getDoctrine()->getRepository('App:Mandate')
            ->getPowersStatistics($mandate);

        $promiseStatistics = $this->getDoctrine()->getRepository('App:Mandate')
            ->getPromiseStatistics($mandate);
        /** @var Promise[] $promises */
        $promises = $this->getDoctrine()->getRepository('App:Promise')
            ->findBy(
                ['mandate' => $mandate, 'published' => true],
                ['madeTime' => 'DESC']
            );

        return [
            'mandate' => $mandate,
            'promise_statistics' => $promiseStatistics,
            'promises' => $promises,
            'power_statistics' => $powersStatistics,
        ];
    }
}
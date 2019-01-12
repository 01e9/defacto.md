<?php

namespace App\Controller;

use App\Entity\Election;
use App\Entity\InstitutionTitle;
use App\Entity\Mandate;
use App\Entity\Promise;
use App\Repository\SettingRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
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
    public function homeAction(Request $request, ManagerRegistry $em)
    {
        return $this->render('app/page/home.html.twig', [
            'president_mandate' => $this->getPresidentMandateData($em),
            'current_election' => $this->getCurrentElectionData($em),
        ]);
    }

    private function getPresidentMandateData(ManagerRegistry $em) : array
    {
        $settingId = SettingRepository::PRESIDENT_INSTITUTION_TITLE_ID;

        /** @var InstitutionTitle $institutionTitle */
        $institutionTitle = $em->getRepository('App:Setting')->get($settingId);

        /** @var Mandate $mandate */
        $mandate = $em->getRepository('App:Mandate')->getLatestByInstitutionTitle($institutionTitle);
        if (!$mandate) {
            throw new \Exception('President mandate is required');
        }

        $powersStatistics = $em->getRepository('App:Mandate')->getPowersStatistics($mandate);

        $promiseStatistics = $em->getRepository('App:Mandate')->getPromiseStatistics($mandate);
        /** @var Promise[] $promises */
        $promises = $em->getRepository('App:Promise')->findBy(
            ['politician' => $mandate->getPolitician(), 'election' => $mandate->getElection(), 'published' => true],
            ['madeTime' => 'DESC']
        );

        return [
            'mandate' => $mandate,
            'promise_statistics' => $promiseStatistics,
            'promises' => $promises,
            'power_statistics' => $powersStatistics,
        ];
    }

    private function getCurrentElectionData(ManagerRegistry $em) : ?array
    {
        $settingId = SettingRepository::CURRENT_ELECTION_ID;

        /** @var Election $election */
        $election = $em->getRepository('App:Setting')->get($settingId);
        if (!$election) {
            return null;
        }

        $constituencies = $em->getRepository('App:Constituency')->createQueryBuilder('con')
            ->innerJoin('con.candidates', 'can', 'WITH', 'can.election = :election')
            ->innerJoin('con.problems', 'prob', 'WITH', 'prob.election = :election')
            ->setParameters(['election' => $election])
            ->groupBy('con.id')
            ->getQuery()
            ->getResult();

        /** @var Mandate[] $mandates */
        $mandates = $em->getRepository('App:Mandate')->findBy([
            'election' => $election,
        ]);

        return [
            'election' => $election,
            'mandates' => $mandates,
            'constituencies' => $constituencies,
        ];
    }
}
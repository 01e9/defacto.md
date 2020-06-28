<?php

namespace App\Controller;

use App\Entity\PromiseAction;
use App\Entity\Election;
use App\Entity\InstitutionTitle;
use App\Entity\Mandate;
use App\Entity\Promise;
use App\Entity\Constituency;
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
            'latest_posts' => $em->getRepository("App:BlogPost")->getRecentPublicPosts(),
        ]);
    }

    private function getPresidentMandateData(ManagerRegistry $em) : ?array
    {
        $settingId = SettingRepository::PRESIDENT_INSTITUTION_TITLE_ID;

        /** @var InstitutionTitle $institutionTitle */
        $institutionTitle = $em->getRepository('App:Setting')->get($settingId);
        if (!$institutionTitle) {
            return null;
        }

        /** @var Mandate $mandate */
        $mandate = $em->getRepository('App:Mandate')->getLatestByInstitutionTitle($institutionTitle);
        if (!$mandate) {
            return null;
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

        $childElections = $em->getRepository('App:Election')->createQueryBuilder('e')
            ->andWhere('e.parent = :election')
            ->orderBy('e.date', 'ASC') // latest will overwrite older in loop below
            ->setParameter('election', $election)
            ->getQuery()
            ->getResult();

        $constituencies = [];
        foreach (array_merge([$election], $childElections) as $el /** @var Election $el */) {
            foreach (
                $em->getRepository('App:Constituency')->createQueryBuilder('con')
                    ->innerJoin('con.candidates', 'can', 'WITH', 'can.election = :election')
                    ->orderBy('con.number', 'ASC')
                    ->groupBy('con.id')
                    ->setParameters(['election' => $el])
                    ->getQuery()
                    ->getResult()
                as $constituency /** @var Constituency $constituency */
            ) {
                $constituencies[ $constituency->getId() ] = [
                    'constituency' => $constituency,
                    'election' => $el,
                ];
            }
        }

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
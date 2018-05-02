<?php

namespace App\Controller;

use App\Entity\InstitutionTitle;
use App\Entity\Mandate;
use App\Entity\Promise;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
     * @Route(path="", name="home")
     * @Method("GET")
     */
    public function homeAction(Request $request)
    {
        /** @var InstitutionTitle $presidentInstitutionTitle */
        $presidentInstitutionTitle = $this->getDoctrine()->getRepository('App:Setting')
            ->get('president_institution_title_id');
        /** @var Mandate $presidentMandate */
        $presidentMandate = $this->getDoctrine()->getRepository('App:Mandate')
            ->getLatestByInstitutionTitle($presidentInstitutionTitle);

        if (!$presidentMandate) {
            throw new \Exception('President mandate is required');
        }

        $presidentMandatePowersStatistics = $this->getDoctrine()->getRepository('App:Mandate')
            ->getPowersStatistics($presidentMandate);

        $presidentMandatePromiseStatistics = $this->getDoctrine()->getRepository('App:Mandate')
            ->getPromiseStatistics($presidentMandate);
        /** @var Promise[] $presidentMandatePromises */
        $presidentMandatePromises = $this->getDoctrine()->getRepository('App:Promise')
            ->findBy(
                ['mandate' => $presidentMandate, 'published' => true],
                ['madeTime' => 'DESC']
            );

        return $this->render('app/page/home.html.twig', [
            'president_mandate' => $presidentMandate,
            'president_mandate_promise_statistics' => $presidentMandatePromiseStatistics,
            'president_mandate_promises' => $presidentMandatePromises,
            'president_mandate_power_statistics' => $presidentMandatePowersStatistics,
        ]);
    }
}
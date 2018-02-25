<?php


namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends Controller
{
    /**
     * @Route(path="/admin", name="admin_index")
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('admin/page/index.html.twig');
    }
}
<?php

namespace App\Controller;

use App\Service\DribbbleAuth;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(Request $request): Response
    {

//        $auth = new DribbbleAuth();
//        $auth->auth();

        $email = $request->get('dribbble-email') ?? null;
        $password = $request->get('dribbble-password') ?? null;

        if ($email && $password) {
            $user = $this->getUser();
            $user->setDribbbleLogin($email);
            $user->setDribbblePassword($password);
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }
}

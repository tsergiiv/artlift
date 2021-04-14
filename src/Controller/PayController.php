<?php


namespace App\Controller;


use App\Service\Pay;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PayController extends AbstractController
{
    /**
     * @Route("/api/shot_stripe_create_session")
     */
    public function createSession(Request $request, Pay $pay)
    {
        return $pay->createSession(
            $this->getParameter("stripe_secret_key"),
            "usd",
            $request,
            $this->getParameter("domain")
        );
    }

    /**
     * @Route ("/api/success/{sessionId}")
     */
    public function success($sessionId, Pay $pay)
    {

        return new JsonResponse($pay->getSession($this->getParameter("stripe_secret_key"), $sessionId), JsonResponse::HTTP_OK);
    }

    /**
     * @Route ("/checkout")
     */
    public function checkout()
    {
        return $this->render("checkout.html.twig", []);
    }

    /**
     * @Route("/api/config")
     */
    public function config()
    {
        return new JsonResponse(['publicKey' => $this->getParameter("stripe_public_key"), 'currency' => "usd"], JsonResponse::HTTP_OK);
    }
}
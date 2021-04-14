<?php

namespace App\Controller;

use App\Entity\DribbbleShotTask;
use App\Entity\DribbleSubscriptionTask;
use App\Repository\DribbbleShotTaskRepository;
use App\Repository\DribbleSubscriptionTaskRepository;
use App\Service\Pay;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PayController extends AbstractController
{
    /**
     * @Route("/api/shot_stripe_create_session")
     */
    public function createSession(Request $request, Pay $pay,EntityManagerInterface $em)
    {
        $task = new DribbbleShotTask();
        $body = json_decode($request->getContent());
        $amount = $body->amount;

        $task->setAmmount($amount/100);
        $task->setPayed(0);
        $task->setCountLikes($body->countLikes);
        $task->setShot($body->shot);

        $result =  $pay->createSession(
            $this->getParameter("stripe_secret_key"),
            "usd",
            $amount,

            $this->getParameter("domain"). '/api/success/{CHECKOUT_SESSION_ID}',
            $this->getParameter("domain")."/api/canceled"

        );
        $task->setSessionId($result['sessionId']);

        $em->persist($task);
        $em->flush();

        return $result['response'];
    }

    /**
     * @Route ("/api/success/{sessionId}")
     */
    public function success($sessionId, Pay $pay,DribbbleShotTaskRepository $rep,EntityManagerInterface $em)
    {
        $task = $rep->findOneBy(['sessionId'=>$sessionId]);
        $task->setPayed(1);

        $em->persist($task);
        $em->flush();

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
     * @Route("/api/sub_stripe_create_session")
     * @param Request $request
     * @param Pay $pay
     * @param EntityManagerInterface $em
     * @return mixed
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function createSubSession(Request $request, Pay $pay, EntityManagerInterface $em)
    {
        $task = new DribbleSubscriptionTask();
        $body = json_decode($request->getContent());
        $amount = $body->amount;

        $task->setAmount($amount / 100);
        $task->setPayed(0);
        $task->setSubId($body->sub_id);
        $task->setUserId($body->user_id);

        $result = $pay->createSession(
            $this->getParameter("stripe_secret_key"),
            "usd",
            $amount,
            $this->getParameter("domain"). "/api/pay-sub/success/{CHECKOUT_SESSION_ID}",
            $this->getParameter("domain"). "/api/pay-sub/canceled"
        );
        $task->setSessionId($result['sessionId']);

        $em->persist($task);
        $em->flush();

        return $result['response'];
    }

    /**
     * @Route ("/api/pay-sub/success/{sessionId}")
     * @param $sessionId
     * @param Pay $pay
     * @param DribbleSubscriptionTask $rep
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function paySubSuccess($sessionId, Pay $pay, DribbleSubscriptionTaskRepository $rep,EntityManagerInterface $em)
    {
        $task = $rep->findOneBy(['session_id' => $sessionId]);
        $task->setPayed(1);

        $em->persist($task);
        $em->flush();

        // return new JsonResponse($pay->getSession($this->getParameter("stripe_secret_key"), $sessionId), JsonResponse::HTTP_OK);
        return $this->render('subscription/sucess.html.twig', []);
    }

    /**
     * @Route ("/api/pay-sub/canceled")
     */
    public function paySubCanceled()
    {
        return $this->render('subscription/canceled.html.twig', []);
    }

    /**
     * @Route("/api/config")
     */
    public function config()
    {
        return new JsonResponse(['publicKey' => $this->getParameter("stripe_public_key"), 'currency' => "usd"], JsonResponse::HTTP_OK);
    }
}
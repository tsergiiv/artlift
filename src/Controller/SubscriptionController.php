<?php

namespace App\Controller;

use App\Entity\Subscription;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/subscribe")
 */
class SubscriptionController extends AbstractController
{
    /**
     * @Route("", name="subscriptions")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();

        $subscriptions = $em->getRepository(Subscription::class)
            ->findBy(['is_shot' => false]);

        return $this->render('subscription/index.html.twig', [
            'subs' => $subscriptions,
        ]);
    }

    /**
     * @Route("/checkout", name="subscriptions_checkout")
     */
    public function checkoutAction(Request $request): Response
    {
        $sub_id = $request->get('sub_id');

        $em = $this->getDoctrine()->getManager();
        $sub = $em->getRepository(Subscription::class)->find($sub_id) ?? null;

        return $this->render('subscription/checkout.html.twig', [
            'sub' => $sub
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Shots;
use App\Entity\Subscription;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/shots")
 */
class ShotsController extends AbstractController
{
    /**
     * @Route("", name="shots")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $shots = $em->getRepository(Shots::class)->findAll();

        return $this->render('shots/index.html.twig', [
            'shots' => $shots
        ]);
    }

    /**
     * @Route("/boost/{shot_id}", name="shots_boost")
     * @param int $shot_id
     * @return Response
     */
    public function boostAction(int $shot_id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $shot = $em->getRepository(Shots::class)->find($shot_id) ?? null;

        if (!$shot) {
            $this->redirectToRoute('shots');
        }

        $subs = $em->getRepository(Subscription::class)
            ->findBy(['is_shot' => true], ['price' => 'asc']);

        return $this->render('shots/boost.html.twig', [
            'shot' => $shot,
            'subs' => $subs
        ]);
    }

    /**
     * @Route("/checkout", name="shots_checkout")
     * @param Request $request
     * @return Response
     */
    public function checkoutAction(Request $request): Response
    {
        $sub_id = $request->get('sub_id');
        $shot_id = $request->get('shot_id');

        $em = $this->getDoctrine()->getManager();
        $sub = $em->getRepository(Subscription::class)->find($sub_id) ?? null;
        $shot = $em->getRepository(Shots::class)->find($shot_id) ?? null;

        return $this->render('shots/checkout.html.twig', [
            'sub'  => $sub,
            'shot' => $shot
        ]);
    }
}

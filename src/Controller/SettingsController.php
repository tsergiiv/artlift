<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\DribbbleShotTaskRepository;
use App\Repository\DribbleSubscriptionTaskRepository;
use App\Repository\ShotsRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController
{
    /**
     * @Route("/settings/profile", name="settings_profile")
     */
    public function index(): Response
    {
        return $this->render('settings/index.html.twig', [
            'controller_name' => 'SettingsController',
        ]);
    }

    /**
     * @Route("/settings/billing", name="settings_billing")
     */
    public function billingAction(ShotsRepository $rep): Response
    {
        $user = $this->getUser();
        $data = [];
        $orderSub = [];
        foreach ($user->getSubscriptions() as $sub) {
            if ($sub->getPayed() == 1) {
                $item["amount"] = $sub->getAmount();
                $item["id"] = $sub->getId();
                $data[] = $item;
            }
            if ($sub->getPayed() != 0) {
                $item["id"] = $sub->getId();
                $item['date'] = date_format(new \DateTime(), 'Y-m-d H:i:s');
                $orderSub[] = $item;
            }
        }
        $data2 = [];
        $orderShot = [];
        foreach ($user->getDribbbleShotTasks() as $task) {
            if ($task->getPayed() == 1) {
                $item["id"] = $task->getId();
                $item["image"] = $rep->find($task->getShot())->getImage();
                $data2[] = $item;
            }
            if ($task->getPayed() != 0) {
                $item["id"] = $task->getId();
                $item['date'] = date_format($task->getPayDate(), 'Y-m-d H:i:s');
                $orderShot[] = $item;
            }
        }
        return $this->render('settings/billing.html.twig', [
            'controller_name' => 'SettingsController',
            'subs' => $data,
            'shots' => $data2,
            'orderSub' => $orderSub,
            'orderShot' => $orderShot,
        ]);
    }

    /**
     * @Route("/api/cancel_sub/{subId}", name="cancel_sub")
     */
    public function cancelSub(DribbleSubscriptionTaskRepository $rep, $subId, EntityManagerInterface $em)
    {
        $sub = $rep->find($subId);
        $sub->setPayed(-1);
        $em->persist($sub);
        $em->flush();

        return $this->redirectToRoute("settings_billing");
    }

    /**
     * @Route("/api/cancel_shot/{shotId}", name="cancel_shot")
     */
    public function cancelShot(DribbbleShotTaskRepository $rep, $shotId, EntityManagerInterface $em)
    {
        $sub = $rep->find($shotId);
        $sub->setPayed(-1);
        $em->persist($sub);
        $em->flush();

        return $this->redirectToRoute("settings_billing");
    }


}

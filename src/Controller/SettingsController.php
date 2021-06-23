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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class SettingsController extends AbstractController
{
    /**
     * @Route("/settings/profile", name="settings_profile")
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $action = $request->get('action') ?? null;
        $fullName = $request->get('username') ?? null;
        $email = $request->get('email') ?? null;
        $oldPassword = $request->get('old-password') ?? null;
        $newPassword = $request->get('password') ?? null;

        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $error = false;
        // update fullName, email, and password
        if ($action == 'save-changed') {
            // update email address
            if ($email != $user->getEmail()) {
                // check the user with the email does not exist
                $is_user_exist = $em->getRepository(User::class)->findOneByEmail($email) ?? null;

                if ($is_user_exist) {
                    $error = true;
                    $this->addFlash('error', $this->getParameter('user_exists_message'));
                } else {
                    $user->setEmail($email);
                }
            }

            // update fullName
            $user->setFullName($fullName);

            // update password
            if ($oldPassword && $newPassword) {
                if ($passwordEncoder->isPasswordValid($user, $oldPassword)) {
                    $encodedPassword = $passwordEncoder->encodePassword($user, $newPassword);
                    $user->setPassword($encodedPassword);
                } else {
                    $error = true;
                    $this->addFlash('error', 'Old password is incorrect');
                }
            }

            if (!$error) {
                $this->addFlash('success', 'The changes were saved');
                $em->flush();
            }
        // erase dribbble account data
        } else if ($action == 'logout-dribbble') {
            $user->setDribbbleLogin(null);
            $user->setDribbblePassword(null);
            $em->flush();
        }

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

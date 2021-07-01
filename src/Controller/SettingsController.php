<?php

namespace App\Controller;

use App\Entity\BillingAddress;
use App\Entity\DribbbleShotTask;
use App\Entity\DribbleSubscriptionTask;
use App\Entity\PaymentMethod;
use App\Entity\ResetPasswordRequest;
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
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SettingsController extends AbstractController
{
    /**
     * @Route("/settings/profile", name="settings_profile")
     */
    public function index(Request $request,
                          UserPasswordEncoderInterface $passwordEncoder,
                          SessionInterface $session): Response
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
        // close account data
        } else if ($action == 'close-account') {
            // remove data from database
            $reset_password_requests = $em->getRepository(ResetPasswordRequest::class)
                ->findBy(['user' => $user]);

            foreach ($reset_password_requests  as $reset_password_request) {
                $em->remove($reset_password_request);
            }

            $this->get('security.token_storage')->setToken(null);
            $session->invalidate(0);

            $em->remove($user);
            $em->flush();

            $this->addFlash('success', 'Your account was closed');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('settings/index.html.twig', [
            'controller_name' => 'SettingsController',
        ]);
    }

    /**
     * @Route("/settings/billing", name="settings_billing")
     */
    public function billingAction(Request $request, ShotsRepository $rep): Response
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $action = $request->get('action');
        $cancel_sub_boost = false;
        if ($action == 'cancel-sub-boost') {
            $sub_id = $request->get('sub-id');
            $subscription = $em->getRepository(DribbleSubscriptionTask::class)->find($sub_id) ?? null;

            if ($subscription) {
                $subscription->setPayed(-1);
                $em->flush();
            }

            $cancel_sub_boost = true;
        } else if ($action == 'cancel-shot-boost') {
            $sub_id = $request->get('shot-sub-id');
            $subscription = $em->getRepository(DribbbleShotTask::class)->find($sub_id) ?? null;

            if ($subscription) {
                $subscription->setPayed(-1);
                $em->flush();
            }
        } else if ($action == 'add-payment-method') {
            $full_name = $request->get('full-name');
            $cvv = $request->get('cvv');
            $card_number = str_replace(' ', '', $request->get('card-number'));
            $expiring_date = explode('/', $request->get('expiring-date'));
            $month = $expiring_date[0];
            $year = $expiring_date[1];

            $payment_method = new PaymentMethod();
            $payment_method->setFullName($full_name);
            $payment_method->setCardNumber($card_number);
            $payment_method->setExpMonth($month);
            $payment_method->setExpYear($year);
            $payment_method->setCvv($cvv);
            $payment_method->setUser($user);

            $em->persist($payment_method);
            $em->flush();
        } else if ($action == 'delete-payment-method') {
            $payment_method_id = $request->get('payment-method-id');
            $payment_method = $em->getRepository(PaymentMethod::class)
                    ->find($payment_method_id) ?? null;

            if ($payment_method) {
                $em->remove($payment_method);
                $em->flush();
            }
        } else if ($action == 'add-billing-address') {
            $street = $request->get('street-address');
            $country = $request->get('country');
            $state = $request->get('state');
            $city = $request->get('city');
            $zip = $request->get('zip-code');

            $billing_address = new BillingAddress();
            $billing_address->setUser($user);
            $billing_address->setStreet($street);
            $billing_address->setCountry($country);
            $billing_address->setState($state);
            $billing_address->setCity($city);
            $billing_address->setZip($zip);

            $em->persist($billing_address);
            $em->flush();
        } else if ($action == 'delete-billing-address') {
            $billing_address = $user->getBillingAddress() ?? null;

            if ($billing_address) {
                $em->remove($billing_address);
                $em->flush();
            }
        }

        $data = [];
        $orderSub = [];
        // get user dribbble boost subscriptions
        foreach ($user->getSubscriptions() as $sub) {
            if ($sub->getPayed() == 1) {
                $data[] = $sub;
            }
            if ($sub->getPayed() == -1) {
                $orderSub[] = $sub;
            }
        }

        $data2 = [];
        $orderShot = [];
        // get user shot boost subscription
        foreach ($user->getDribbbleShotTasks() as $sub) {
            if ($sub->getPayed() == 1) {
                $data2[] = $sub;
            }
            if ($sub->getPayed() == -1) {
                $orderShot[] = $sub;
            }
        }

        // get user payment methods
        $paymentMethods = $em->getRepository(PaymentMethod::class)
            ->findBy(['user' => $user]) ?? null;

        // get user billing address
        $billing_address = $em->getRepository(BillingAddress::class)
            ->findOneBy(['user' => $user]) ?? null;

        return $this->render('settings/billing.html.twig', [
            'controller_name'  => 'SettingsController',
            'subs'             => $data,
            'shots'            => $data2,
            'orderSub'         => $orderSub,
            'orderShot'        => $orderShot,
            'cancel_sub_boost' => $cancel_sub_boost,
            'payment_methods'  => $paymentMethods,
            'billing_address'  => $billing_address,
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

<?php

namespace App\Controller;

use App\Entity\User;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;

class FacebookRegController extends AbstractController
{
    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/signup/facebook", name="signup_facebook_start")
     */
    public function signupAction(ClientRegistry $clientRegistry)
    {
        // on Symfony 3.3 or lower, $clientRegistry = $this->get('knpu.oauth2.registry');

        // will redirect to Facebook!
        return $clientRegistry
            ->getClient('facebook_reg') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect();
    }

    /**
     * After going to Facebook, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     * @Route("/signup/facebook/check", name="signup_facebook_check")
     */
    public function signupCheckAction(Request $request, ClientRegistry $clientRegistry, UserPasswordEncoderInterface $passwordEncoder)
    {
        /** @var \KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient $client */
        $client = $clientRegistry->getClient('facebook_reg');

        try {
            // the exact class depends on which provider you're using
            /** @var \League\OAuth2\Client\Provider\FacebookUser $facebook_user */
            $facebook_user = $client->fetchUser();

            $em = $this->getDoctrine()->getManager();

            // check if user already registrered
            $user = $em->getRepository(User::class)
                ->findOneBy(['email' => $facebook_user->getEmail()]) ?? null;

            if ($user) {
                $this->addFlash('error', $this->getParameter('user_exists_message'));
                return $this->redirectToRoute('app_login');
            }

            $new_user = new User();
            $new_user->setFullName($facebook_user->getName());
            $new_user->setEmail($facebook_user->getEmail());
            $new_user->setFacebookid($facebook_user->getId());
            $new_user->setIsVerified(1);

            $new_user->setPassword(
                $passwordEncoder->encodePassword(
                    $new_user,
                    $facebook_user->getId()
                )
            );

            $em->persist($new_user);
            $em->flush();

            $this->addFlash('success', $this->getParameter('success_register_message'));
            return $this->redirectToRoute('app_login');
        } catch (IdentityProviderException $e) {
            // something went wrong!
            // probably you should return the reason to the user
            var_dump($e->getMessage()); die;
        }
    }
}
<?php

namespace App\Controller;

use App\Entity\User;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class GoogleRegController extends AbstractController
{
    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/signup/google", name="signup_google_start")
     */
    public function signupAction(ClientRegistry $clientRegistry)
    {
        // on Symfony 3.3 or lower, $clientRegistry = $this->get('knpu.oauth2.registry');

        // will redirect to Google!
        return $clientRegistry
            ->getClient('google_reg') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect();
    }

    /**
     * After going to Google, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     * @Route("/signup/google/check", name="signup_google_check")
     */
    public function signupCheckAction(Request $request, ClientRegistry $clientRegistry, UserPasswordEncoderInterface $passwordEncoder)
    {
        /** @var \KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient $client */
        $client = $clientRegistry->getClient('google_reg');

        try {
            // the exact class depends on which provider you're using
            /** @var \League\OAuth2\Client\Provider\GoogleUser $google_user */
            $google_user = $client->fetchUser();

            $em = $this->getDoctrine()->getManager();

            // check if user already registrered
            $user = $em->getRepository(User::class)
                ->findOneBy(['email' => $google_user->getEmail()]) ?? null;

            if ($user) {
                return $this->redirectToRoute('app_login');
            }

            $new_user = new User();
            $new_user->setFullName($google_user->getName());
            $new_user->setEmail($google_user->getEmail());
            $new_user->setGoogleid($google_user->getId());
            $new_user->isVerified(1);

            $new_user->setPassword(
                $passwordEncoder->encodePassword(
                    $new_user,
                    $google_user->getId()
                )
            );

            $em->persist($new_user);
            $em->flush();

            return $this->redirectToRoute('app_login');
        } catch (IdentityProviderException $e) {
            // something went wrong!
            // probably you should return the reason to the user
            var_dump($e->getMessage()); die;
        }
    }
}
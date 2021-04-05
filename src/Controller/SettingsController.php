<?php

namespace App\Controller;

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
    public function billingAction(): Response
    {
        return $this->render('settings/billing.html.twig', [
            'controller_name' => 'SettingsController',
        ]);
    }
}

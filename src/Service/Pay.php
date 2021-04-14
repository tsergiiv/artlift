<?php


namespace App\Service;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

class Pay
{
    /**
     * @param $stripeSecretKey
     * @param $currency
     * @param Request $request
     * @param $url
     * @return JsonResponse
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function createSession($stripeSecretKey, $currency, Request $request, $url)
    {
        \Stripe\Stripe::setApiKey($stripeSecretKey);
        $body = json_decode($request->getContent());
        $amount = $body->amount;

        $checkoutSession = \Stripe\Checkout\Session::create([
            'success_url' => $url . '/api/success/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $url . '/api/canceled',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'name' => 'Invoice Payment',
                'images' => ["https://picsum.photos/300/300?random=4"],
                'quantity' => 1,
                'amount' => $amount,
                'currency' => $currency
            ]]
        ]);

        return new JsonResponse(['sessionId' => $checkoutSession['id']], JsonResponse::HTTP_OK);

    }

    public function getSession($stripeSecretKey, $sessionId)
    {
        \Stripe\Stripe::setApiKey($stripeSecretKey);
        $checkout_session = \Stripe\Checkout\Session::retrieve($sessionId);
        return $checkout_session;

    }


}
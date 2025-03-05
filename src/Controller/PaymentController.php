<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PayPal\Api\Payer;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;

#[Route('/payment')]
class PaymentController extends AbstractController
{
    #[Route('', name: 'payment_checkout')]
    public function checkout(): Response
    {
        // No need to implement this for the testing purpose
        return new Response("Checkout Rendered. Use /pay to test payment.");
    }

    #[Route('/pay', name: 'payment_process')]
    public function pay(): Response
    {
        // Create a dummy payment of $10.00 for testing purposes
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        $amount = new Amount();
        $amount->setTotal('10.00'); // This is the dummy value for testing
        $amount->setCurrency("USD");

        $transaction = new Transaction();
        $transaction->setAmount($amount)
                    ->setDescription("Dummy Order Payment");

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl('http://127.0.0.1:8000/payment/success') // Dummy success URL
                     ->setCancelUrl('http://127.0.0.1:8000/payment/cancel'); // Dummy cancel URL

        $payment = new Payment();
        $payment->setIntent("sale")
                ->setPayer($payer)
                ->setTransactions([$transaction])
                ->setRedirectUrls($redirectUrls);

        try {
            // Create the payment
            $payment->create($this->getPayPalClient());

            // Redirect user to PayPal
            return $this->redirect($payment->getApprovalLink());
        } catch (\Exception $ex) {
            return new Response($ex->getMessage());
        }
    }

    #[Route('/success', name: 'payment_success')]
    public function success(): Response
    {
        return new Response("Payment was successful!"); // You can enhance this view
    }

    #[Route('/cancel', name: 'payment_cancel')]
    public function cancel(): Response
    {
        return new Response("Payment was canceled."); // You can enhance this view as need be
    }

    private function getPayPalClient()
    {
        return new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                $_ENV['PAYPAL_CLIENT_ID'], 
                $_ENV['PAYPAL_SECRET']
            )
        );
    }
}
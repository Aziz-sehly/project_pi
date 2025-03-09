<?php

namespace App\Controller;

use App\Entity\Order;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use PayPal\Api\Payer;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/payment')]
class PaymentController extends AbstractController
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    #[Route('', name: 'payment_checkout')]
    public function checkout(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cart = $request->getSession()->get('cart', []);
        if (!$cart) {
            return $this->redirectToRoute('shop'); // Redirect if cart is empty
        }

        $total = 0;
        $products = [];
        foreach ($cart as $productId => $data) {
            $total += $data['price'] * $data['quantity'];
            $products[] = [
                'product_id' => $productId,
                'name' => $data['name'],
                'quantity' => $data['quantity'],
                'price' => $data['price'],
            ];
        }

        $order = new Order();
        $order->setCustomerName("Guest User")
            ->setCustomerEmail("guest@example.com")
            ->setTotal($total)
            ->setCreatedAt(new DateTimeImmutable())
            ->setStatus('pending')
            ->setProducts($products); // Store products as an array

        $entityManager->persist($order);
        $entityManager->flush();

        return $this->render('payment/checkout.html.twig', [
            'order' => $order,
            'products' => $products,
        ]);
    }

    #[Route('/pay', name: 'payment_process')]
    public function pay(SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        if (!$cart) {
            return $this->redirectToRoute('shop');
        }

        $total = 0;
        foreach ($cart as $data) {
            $total += $data['price'] * $data['quantity'];
        }

        if (!is_numeric($total)) {
            return new Response("Error: Total amount is not numeric.");
        }

        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        $amount = new Amount();
        $amount->setTotal($total);
        $amount->setCurrency("USD");

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setDescription("Order Payment");

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($this->urlGenerator->generate('payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL))
            ->setCancelUrl($this->urlGenerator->generate('payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL));

        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setTransactions([$transaction])
            ->setRedirectUrls($redirectUrls);

        try {
            $payment->create($this->getPayPalClient());
            return $this->redirect($payment->getApprovalLink());
        } catch (Exception $ex) {
            return new Response($ex->getMessage());
        }
    }

    #[Route('/success', name: 'payment_success')]
    public function success(SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        $session->set('cart', []);
        return $this->render('shop/front/payment/success.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('/cancel', name: 'payment_cancel')]
    public function cancel(SessionInterface $session): Response
    {
        $session->set('cart', []);
        return $this->render('shop/front/payment/cancel.html.twig');
    }

    private function getPayPalClient(): ApiContext
    {
        return new ApiContext(
            new OAuthTokenCredential(
                $_ENV['PAYPAL_CLIENT_ID'],
                $_ENV['PAYPAL_SECRET']
            )
        );
    }
}
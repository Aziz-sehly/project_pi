<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function checkout(ProductRepository $productRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $cart = $request->getSession()->get('cart', []);
        if (!$cart) {
            return $this->redirectToRoute('shop'); // Redirect if cart is empty
        }

        $total = 0;
        $products = [];
        foreach ($cart as $productId => $quantity) {
            $product = $productRepository->find($productId);
            if ($product) {
                $total += $product->getPrice() * $quantity;
                $products[] = [
                    'product_id' => $product->getId(),
                    'name' => $product->getName(),
                    'quantity' => $quantity,
                    'price' => $product->getPrice(),
                ];
            }
        }

        $order = new Order();
        $order->setCustomerName("Guest User")
              ->setCustomerEmail("guest@example.com")
              ->setTotal($total)
              ->setCreatedAt(new \DateTimeImmutable())
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
    public function pay(SessionInterface $session, ProductRepository $productRepository): Response
    {
        $cart = $session->get('cart', []);
        if (!$cart) {
            return $this->redirectToRoute('shop'); // Redirect if cart is empty
        }

        $total = 0;
        $products = [];  
        foreach ($cart as $productId => $quantity) {
            $product = $productRepository->find($productId);
            if ($product) {
                $total += $product->getPrice() * $quantity;
                $products[] = [
                    'id' => $productId,
                    'quantity' => $quantity,
                    'price' => $product->getPrice(),
                    'name' => $product->getName()
                ]; 
            }
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
        } catch (\Exception $ex) {
            return new Response($ex->getMessage());
        }
    }

    #[Route('/success', name: 'payment_success')]
    public function success(SessionInterface $session): Response
    {
        $session->set('cart', []); 
        return $this->render('payment/success.html.twig');
    }

    #[Route('/cancel', name: 'payment_cancel')]
    public function cancel(): Response
    {
        return $this->render('payment/cancel.html.twig');
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
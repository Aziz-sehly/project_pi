<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;

final class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart')]
    public function cart(SessionInterface $session, ProductRepository $productRepository): Response
    {
        $cart = $session->get('cart', []);

        $cartWithData = [];
        foreach ($cart as $id => $quantity) {
            $cartWithData[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity,
            ];
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $cartWithData,
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function addToCart($id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        $cart[$id] = ($cart[$id] ?? 0) + 1;
        $session->set('cart', $cart);

        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function removeFromCart($id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
        }
        $session->set('cart', $cart);

        return $this->redirectToRoute('cart');
    }

}

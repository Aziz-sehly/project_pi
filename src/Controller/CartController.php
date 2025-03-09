<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ProductRepository;

#[Route('/cart')]
final class CartController extends AbstractController
{
    #[Route('/add', name: 'cart_add', methods: ['POST'])]
    public function addToCart(Request $request, SessionInterface $session, ProductRepository $productRepository): Response
    {
        $productId = $request->request->get('product_id');
        $quantity = (int)$request->request->get('quantity');
        if (empty($productId) || empty($quantity)) {
            return new JsonResponse(['error' => 'Invalid data'], 400);
        }

        $product = $productRepository->find($productId);

        if (!$product) {
            return $this->redirectToRoute('shop');
        }
        if ($quantity <= 0) {
            return $this->redirectToRoute('shop');
        }

        // Retrieve or initialize cart from session
        $cart = $session->get('cart', []);

        // Add or update product in cart
        if (isset($cart[$product->getId()])) {
            $cart[$product->getId()]['quantity'] += $quantity;
        } else {
            $cart[$product->getId()] = [
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'quantity' => $quantity,
            ];
        }

        // Save cart back to session
        $session->set('cart', $cart);

        return $this->redirectToRoute('shop_cart');
    }

    #[Route('/remove/{id}', name: 'cart_remove', methods: ['POST'])]
    public function removeFromCart($id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
        }
        $session->set('cart', $cart);

        return $this->redirectToRoute('shop_cart');
    }

}

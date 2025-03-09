<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/shop')]
class ShopController extends AbstractController
{
    #[Route('/', name: 'shop', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('shop/front/index.html.twig', [
            'products' => $productRepository->findBy([], ['createdAt' => 'DESC'], 8),
        ]);
    }

    #[Route('/cart', name: 'shop_cart', methods: ['GET'] )]
    public function cart(SessionInterface $session): Response
    {
        return $this->render('shop/front/cart/index.html.twig', [
            'cart' => $session->get('cart', []),
        ]);
    }

    #[Route('/{id}', name: 'shop_product_detail', methods: ['GET'])]
    public function productDetail(Product $product): Response
    {
        return $this->render('shop/front/product/detail.html.twig', [
            'product' => $product,
        ]);
    }

}

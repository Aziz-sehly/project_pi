<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ShopAdminController extends AbstractController
{
    #[Route('/shop_admin', name: 'shop_admin')]
    public function index(): Response
    {
        return $this->render('shop/admin/index.html.twig');
    }
}

<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Product;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface; 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/commande', name: 'app_commande_index')]
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }

    #[Route('/commande/new/{id}', name: 'app_commande_new')]
    public function new(Request $request, Product $product): Response
    {
        $commande = new Commande();
        $commande->setProduct($product);
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commande->setOrderDate(new \DateTime());

            
            $this->entityManager->persist($commande);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_commande_index');
        }

        return $this->render('commande/new.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }

    #[Route('/shop', name: 'app_shop_index')]
    public function shop(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll(); 
        return $this->render('commande/shop.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/commande/edit/{id}', name: 'app_commande_edit')]
    public function edit(Request $request, Commande $commande): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->entityManager->flush();

            return $this->redirectToRoute('app_commande_index');
        }

        return $this->render('commande/edit.html.twig', [
            'form' => $form->createView(),
            'commande' => $commande,
        ]);
    }

    #[Route('/commande/cancel/{id}', name: 'app_commande_cancel')]
    public function cancel(Commande $commande): Response
    {
        
        $commande->setCancellationDate(new \DateTime());

        
        $this->entityManager->flush();

        return $this->redirectToRoute('app_commande_index');
    }

}

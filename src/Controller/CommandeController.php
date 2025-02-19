<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Commande;
use App\Form\CommandeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;

class CommandeController extends AbstractController
{
    #[Route('/commandes', name: 'app_commande_index')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $commandes = $doctrine->getRepository(Commande::class)->findAll();

        return $this->render('commande/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[Route('/commande/new', name: 'app_commande_new')]
    public function new(Request $request, ManagerRegistry $doctrine): Response
    {
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($commande);
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_show', ['id' => $commande->getId()]);
        }

        return $this->render('commande/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/commande/{id}', name: 'app_commande_show', requirements: ['id' => '\d+'])]
    public function show(int $id, ManagerRegistry $doctrine): Response
    {
        $commande = $doctrine->getRepository(Commande::class)->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('No commande found for id ' . $id);
        }

        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/commande/edit/{id}', name: 'app_commande_edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, Request $request, ManagerRegistry $doctrine): Response
    {
        $commande = $doctrine->getRepository(Commande::class)->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('No commande found for id ' . $id);
        }

        $form = $this->createForm(CommandeType::class, $commande);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine->getManager()->flush();

            return $this->redirectToRoute('app_commande_show', ['id' => $commande->getId()]);
        }

        return $this->render('commande/edit.html.twig', [
            'form' => $form->createView(),
            'commande' => $commande,
        ]);
    }

    #[Route('/commande/delete/{id}', name: 'app_commande_delete', requirements: ['id' => '\d+'])]
    public function delete(int $id, ManagerRegistry $doctrine): Response
    {
        $commande = $doctrine->getRepository(Commande::class)->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('No commande found for id ' . $id);
        }

        $entityManager = $doctrine->getManager();
        $entityManager->remove($commande);
        $entityManager->flush();

        return $this->redirectToRoute('app_commande_index');
    }

    #[Route('/commande/add/{productId}/{quantity}', name: 'app_commande_add')]
    public function add(int $productId, int $quantity, ManagerRegistry $doctrine): Response
    {
        $session = $this->get('session');
        $cart = $session->get('cart', []);
        $product = $doctrine->getRepository(Product::class)->find($productId);

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $cart[$productId] = [
            'product' => $product,
            'quantity' => $quantity,
        ];

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_commande_new');
    }

    private function getProducts()
    {
        return [
            [
                'id' => 1,
                'name' => 'Product 1',
                'description' => 'Description for Product 1',
                'price' => 10.00,
            ],
            [
                'id' => 2,
                'name' => 'Product 2',
                'description' => 'Description for Product 2',
                'price' => 20.00,
            ],
        ];
    }
}
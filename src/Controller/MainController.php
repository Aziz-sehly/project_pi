<?php

namespace App\Controller;
// src/Controller/MainController.php
namespace App\Controller;

use App\Entity\Hotel;
use App\Entity\Evenement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    
    #[Route('/routefront', name: 'app_front')]
public function index(EntityManagerInterface $em): Response
{
    $hotels = $em->getRepository(Hotel::class)->findAll();
    $evenements = $em->getRepository(Evenement::class)->findAll(); 

    return $this->render('front.html.twig', [
        'hotels' => $hotels,
        'evenements' => $evenements, 
    ]);
}

}
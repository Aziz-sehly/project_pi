<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(): Response
    {

        return $this->render('home/index.html.twig');
    }

    /**
     * @Route("/artisan", name="artisan")
     */
    public function artisan(): Response
    {

        return $this->render('artisan/index.html.twig');
    }

    /**
     * @Route("/organisateur", name="organisateur")
     */
    public function organisateur(): Response
    {

        return $this->render('organisateur/index.html.twig');
    }
}

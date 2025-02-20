<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Attraction;
use App\Form\AttractionType;
use App\Repository\AttractionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/attraction')]
final class AttractionController extends AbstractController
{
    
    #[Route(name: 'app_attraction_index', methods: ['GET'])]
    public function index(AttractionRepository $attractionRepository): Response
    {
        return $this->render('attraction/list.html.twig', [
            'attractions' => $attractionRepository->findAll(),
        ]);
    }

    #[Route('/front', name: 'app_attraction', methods: ['GET', 'POST'])]
    public function att_index(AttractionRepository $attractionRepository): Response
    {
        return $this->render('attraction/index.html.twig', [
            'attractions' => $attractionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_attraction_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $attraction = new Attraction();
        $form = $this->createForm(AttractionType::class, $attraction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                // Generate a unique filename
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                // Move the file to the directory where images are stored
                $imageFile->move(
                    $this->getParameter('images_directory'), // Directory defined in services.yaml
                    $newFilename
                );

                // Set the image field to the new filename
                $attraction->setImage($newFilename);
            }

            $entityManager->persist($attraction);
            $entityManager->flush();

            return $this->redirectToRoute('app_attraction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('attraction/new.html.twig', [
            'attraction' => $attraction,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_attraction_show', methods: ['GET'])]
    public function show(Attraction $attraction): Response
    {
        return $this->render('attraction/show.html.twig', [
            'attraction' => $attraction,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_attraction_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Attraction $attraction, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AttractionType::class, $attraction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload if a new file is uploaded
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                // Generate a unique filename
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                // Move the file to the directory where images are stored
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                // Set the image field to the new filename
                $attraction->setImage($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_attraction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('attraction/edit.html.twig', [
            'attraction' => $attraction,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_attraction_delete', methods: ['POST'])]
    public function delete(Request $request, Attraction $attraction, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $attraction->getId(), $request->request->get('_token'))) {
            $entityManager->remove($attraction);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_attraction_index', [], Response::HTTP_SEE_OTHER);
    }
}

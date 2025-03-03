<?php

namespace App\Controller;

use App\Entity\Hotel;
use App\Form\HotelType;
use App\Repository\HotelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Booking;
use App\Entity\Rating;
use App\Form\RatingType;



#[Route('/hotel')]
final class HotelController extends AbstractController
{
    #[Route('/listes', name: 'hotel_liste', methods: ['GET'])]
    public function liste(HotelRepository $hotelRepository): Response
    {
        $hotels = $hotelRepository->findAll();

        return $this->render('hotel/display.html.twig', [
            'hotels' => $hotels,
            
        ]);
    }
    

    #[Route(name: 'app_hotel_index', methods: ['GET'])]
    public function index(HotelRepository $hotelRepository): Response
{
    $hotels = $hotelRepository->findAll();

    return $this->render('hotel/index.html.twig', [
        'hotels' => $hotels,
    ]);
}

    #[Route('/new', name: 'app_hotel_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $hotel = new Hotel();
        $form = $this->createForm(HotelType::class, $hotel);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $ImagesFile = $form->get('Images')->getData(); // Get the file from the form
    
            if ($ImagesFile) {
                try {
                    // Generate a unique filename
                    $newFilename = uniqid() . '.' . $ImagesFile->guessExtension();
    
                    // Specify the directory where the file will be saved
                    $uploadDirectory = $this->getParameter('hotel_Images_directory');
                    
                    // Move the uploaded file to the specified directory
                    $ImagesFile->move($uploadDirectory, $newFilename);
                    
                    // Save the file name in the hotel entity
                    $hotel->setImages($newFilename);
                } catch (FileException $e) {
                    // Handle any exceptions (e.g., file is too large, invalid format, etc.)
                    // You could log the error or show a message to the user
                }
            }
    
            // Persist hotel data
            $entityManager->persist($hotel);
            $entityManager->flush();
    
            // Redirect to hotel index page after successful form submission
            return $this->redirectToRoute('app_hotel_index', [], Response::HTTP_SEE_OTHER);
        }
    
        // Render form if it's not submitted or invalid
        return $this->render('hotel/new.html.twig', [
            'hotel' => $hotel,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_hotel_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Hotel $hotel): Response
    {
        return $this->render('hotel/show.html.twig', [
            'hotel' => $hotel,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_hotel_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Hotel $hotel, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HotelType::class, $hotel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_hotel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('hotel/edit.html.twig', [
            'hotel' => $hotel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_hotel_delete', methods: ['POST'])]
    public function delete(Request $request, Hotel $hotel, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $hotel->getId(), $request->request->get('_token'))) {
            // Fetch all bookings related to this hotel using 'id_hotel'
            $bookings = $entityManager->getRepository(Booking::class)->findBy(['id_hotel' => $hotel]);

            // Remove all bookings associated with the hotel
            foreach ($bookings as $booking) {
                $entityManager->remove($booking);
            }

            // Now delete the hotel
            $entityManager->remove($hotel);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_hotel_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/rate', name: 'app_hotel_rate', methods: ['GET', 'POST'])]
    public function rate(Request $request, Hotel $hotel, EntityManagerInterface $entityManager): Response
    {
        $rating = new Rating();
        // Optionally, set values here if you want them auto-set:
        $rating->setCreatedAt(new \DateTime());
        $rating->setHotel($hotel);
    
        $form = $this->createForm(RatingType::class, $rating);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rating);
            $entityManager->flush();
    
            return $this->redirectToRoute('hotel_liste', ['id' => $hotel->getId()]);
        }
    
        return $this->render('hotel/rate.html.twig', [
            'hotel' => $hotel,
            'form'  => $form->createView(),
        ]);
    }
    
}
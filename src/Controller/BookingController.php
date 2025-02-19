<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\HotelRepository;


#[Route('/booking')]
class BookingController extends AbstractController
{
    #[Route(name: 'app_booking_index', methods: ['GET'])]
    public function index(BookingRepository $bookingRepository): Response
    {
        return $this->render('booking/index.html.twig', [
            'bookings' => $bookingRepository->findAll(),
        ]);
    }

    #[Route('/new/{id?}', name: 'app_booking_new', methods: ['GET', 'POST'])]
public function new(
    Request $request,
    EntityManagerInterface $entityManager,
    HotelRepository $hotelRepository,
    ?int $id = null
): Response {
    $booking = new Booking();
    $disableHotelField = false;

    if ($id !== null) {
        $hotel = $hotelRepository->find($id);
        if ($hotel) {
            // Set the clicked hotel on the booking
            $booking->setIdHotel($hotel);
            // Optionally, disable editing the hotel field in the form
            $disableHotelField = true;
        } else {
            $this->addFlash('error', 'Hotel not found');
            return $this->redirectToRoute('app_booking_index');
        }
    }

    // Pass an option to the form to disable the hotel field if needed
    $form = $this->createForm(BookingType::class, $booking, [
        'disable_hotel' => $disableHotelField,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        try {
            $entityManager->persist($booking);
            $entityManager->flush();
            $this->addFlash('success', 'Booking created successfully!');
            return $this->redirectToRoute('app_booking_new', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error occurred while creating booking: '.$e->getMessage());
        }
    }

    return $this->render('booking/new.html.twig', [
        'booking' => $booking,
        'form' => $form,
    ]);
}


    #[Route('/{id}', name: 'app_booking_show', methods: ['GET'])]
    public function show(Booking $booking): Response
    {
        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_booking_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Booking $booking, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();
                $this->addFlash('success', 'Booking updated successfully!');
                return $this->redirectToRoute('app_booking_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error occurred while updating booking: '.$e->getMessage());
            }
        }

        return $this->render('booking/edit.html.twig', [
            'booking' => $booking,
            'form' => $form,
        ]);
    }
    #[Route('/{id}', name: 'app_booking_delete', methods: ['POST'])]
public function delete(Request $request, Booking $booking, EntityManagerInterface $entityManager): Response
{
    if ($this->isCsrfTokenValid('delete'.$booking->getId(), $request->request->get('_token'))) {
        try {
            $entityManager->remove($booking);
            $entityManager->flush();
            $this->addFlash('success', 'Booking deleted successfully!');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error occurred while deleting booking: '.$e->getMessage());
        }
    } else {
        $this->addFlash('error', 'Invalid CSRF token');
    }
    
    return $this->redirectToRoute('app_booking_index');
}

    
}
?>
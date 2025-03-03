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
use Knp\Component\Pager\PaginatorInterface;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Dompdf\Dompdf;
use Dompdf\Options;



#[Route('/booking')]
class BookingController extends AbstractController
{
    
    #[Route('/', name: 'app_booking_index', methods: ['GET'])]
public function index(Request $request, BookingRepository $bookingRepository, PaginatorInterface $paginator): Response
{
    // Construire la requête
    $query = $bookingRepository->createQueryBuilder('b')
        ->getQuery();

    // Paginer les résultats
    $bookings = $paginator->paginate(
        $query, // La requête Doctrine ou le tableau d'objets
        $request->query->getInt('page', 1), // Numéro de page, par défaut 1
        10 // Nombre d'éléments par page
    );

    return $this->render('booking/index.html.twig', [
        'bookings' => $bookings,
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

            // Redirect to the choice page so the user can decide to pay or not
            return $this->redirectToRoute('app_booking_choice', ['id' => $booking->getId()], Response::HTTP_SEE_OTHER);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error occurred while creating booking: ' . $e->getMessage());
        }
    }

    return $this->render('booking/new.html.twig', [
        'booking' => $booking,
        'form' => $form,
        'stripe_public_key' => $this->getParameter('stripe_public_key'),
    ]);
}

#[Route('/{id}/payment', name: 'app_booking_payment', methods: ['GET'])]
public function payment(Booking $booking): Response
{
    return $this->render('booking/payment.html.twig', [
        'booking' => $booking,
        'stripe_public_key' => $this->getParameter('stripe_public_key'),
    ]);
}

#[Route('/{id}/choice', name: 'app_booking_choice', methods: ['GET'])]
public function choice(Booking $booking): Response
{
    return $this->render('booking/choice.html.twig', [
        'booking' => $booking,
        'stripe_public_key' => $this->getParameter('stripe_public_key'),
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
   
#[Route('/{id}/pdf', name: 'app_booking_pdf')]
public function generatePdf(Booking $booking): Response
{
    // Configuration de Dompdf
    $pdfOptions = new Options();
    $pdfOptions->set('defaultFont', 'Arial');
    $pdfOptions->set('isRemoteEnabled', true);
    
    $dompdf = new Dompdf($pdfOptions);
    
    // Rendu HTML
    $html = $this->renderView('booking/pdf.html.twig', [
        'booking' => $booking
    ]);
    
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Génération du nom de fichier
    $filename = sprintf('reservation-%d-%s.pdf', 
        $booking->getId(), 
        (new \DateTime())->format('Y-m-d')
    );

    // Envoi du PDF en réponse
    return new Response(
        $dompdf->output(),
        Response::HTTP_OK,
        [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"'
        ]
    );
}
    
}

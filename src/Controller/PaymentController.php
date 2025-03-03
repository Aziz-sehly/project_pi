<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Hotel;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{
    #[Route('/checkout/{id}', name: 'checkout')]
    public function checkout(Booking $booking): JsonResponse
{
    Stripe::setApiKey($this->getParameter('stripe_secret_key'));

    // Retrieve the necessary details for booking
    $NumberOfGuest = $booking->getNumberOfGuest();
    $checkIn = $booking->getCheckIn();
    $checkOut = $booking->getCheckOut();

    if (!$checkIn instanceof \DateTime || !$checkOut instanceof \DateTime) {
        return new JsonResponse(['error' => 'Invalid check-in or check-out date.'], Response::HTTP_BAD_REQUEST);
    }

    $diff = $checkOut->diff($checkIn);
    $nights = $diff->days;

    if ($nights <= 0) {
        return new JsonResponse(['error' => 'Invalid duration for stay.'], Response::HTTP_BAD_REQUEST);
    }

    $hotel = $booking->getIdHotel();
    $pricePerNight = $hotel->getPrix();
    $priceInCents = $pricePerNight * $nights * $NumberOfGuest * 100;

    $session = Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => 'Réservation Hôtel: ' . $hotel->getNom(),
                ],
                'unit_amount' => $priceInCents,
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => $this->generateUrl('payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
        'cancel_url' => $this->generateUrl('payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
    ]);

    return new JsonResponse(['id' => $session->id]);
}


#[Route('/payment-success', name: 'payment_success')]
public function success(): Response
{
    return $this->render('payment/success.html.twig');
}


    #[Route('/payment-cancel', name: 'payment_cancel')]
    public function cancel(): Response
    {
        return $this->render('payment/cancel.html.twig');
    }
    #[Route('/stripe/webhook', name: 'stripe_webhook', methods: ['POST'])]
public function webhook(Request $request, EntityManagerInterface $em): Response
{
    Stripe::setApiKey($this->getParameter('stripe_secret_key'));

    $payload = $request->getContent();
    $sigHeader = $request->headers->get('Stripe-Signature');
    $endpointSecret = $this->getParameter('stripe_webhook_secret');

    try {
        $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
    } catch (\UnexpectedValueException $e) {
        return new Response('Invalid payload', 400);
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
        return new Response('Invalid signature', 400);
    }

    // Handle the event
    if ($event->type === 'checkout.session.completed') {
        $session = $event->data->object; // This is the Checkout Session object
        // Process the successful payment here
        $bookingId = $session->client_reference_id;  // Get the booking ID from the session
        $booking = $em->getRepository(Booking::class)->find($bookingId);

        if ($booking) {
            // Update booking status or any other action after payment success
            $booking->setStatus('paid');
            $em->flush();
        }
    }

    return new Response('Webhook handled', 200);
}

}

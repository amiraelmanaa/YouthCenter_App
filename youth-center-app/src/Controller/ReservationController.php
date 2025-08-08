<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\RoomRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Booking;
use App\Entity\Room;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use App\Repository\BookingRepository;



final class ReservationController extends AbstractController
{

#[Route('/reservation', name: 'reservation')]
public function index(Request $request, RoomRepository $roomRepository): Response
{
    $rooms = [];
    $start = null;
    $end = null;
    $guests = null;
    $isGroup = false;

    if ($request->query->has('start_date')) {
        $start = new \DateTime($request->query->get('start_date'));
        $end = new \DateTime($request->query->get('end_date'));
        $guests = (int) $request->query->get('guests');
        $isGroup = $request->query->has('is_group');

        $rooms = $roomRepository->findAvailableRooms($start, $end, $guests, $isGroup);
    }

    if ($request->isXmlHttpRequest()) {
        $roomData = array_map(function ($room) {
            return [
                'id' => $room->getId(),
                'capacity' => $room->getCapacity(),
                'pricePerNight' => $room->getPricePerNight(),
                'isGroupOnly' => $room->isGroupOnly(),
            ];
        }, $rooms);

        return new JsonResponse($roomData);
    }

    return $this->render('reservation/index.html.twig', [
        'rooms' => $rooms,
    ]);
}
#[Route('/booking', name: 'booking_create', methods: ['POST'])]
public function createBooking(Request $request, RoomRepository $roomRepository, EntityManagerInterface $em): JsonResponse
{ try{
    $data = json_decode($request->getContent(), true);

    $room = $roomRepository->find($data['room_id']);

    if (!$room) {
        return new JsonResponse(['error' => 'Room not found'], 404);
    }

    $booking = new Booking();
    $booking->setRoom($room);
    $booking->setStartDate(new \DateTime($data['start_date']));
    $booking->setEndDate(new \DateTime($data['end_date']));
    $booking->setGuestsCount($data['guests']);
    $booking->setIsGroupBooking($data['is_group']);
    $booking->setGuestName($data['first_name']);
    $booking->setGuestLastName($data['last_name']);
    $booking->setAge($data['age']);
    $booking->setEmail($data['email']);
    $booking->setStatus('pending');
    $booking->updateTotalPrice();
    $em->persist($booking);
    $em->flush();

    return new JsonResponse(['success' => true]);
}
catch (\Throwable $e) {
    return new JsonResponse(['error' => $e->getMessage()], 500);
}



}
}
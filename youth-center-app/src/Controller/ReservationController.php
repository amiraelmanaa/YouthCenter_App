<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\RoomRepository;

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

        return $this->render('reservation/index.html.twig', [
            'rooms' => $rooms,
            'start' => $start,
            'end' => $end,
            'guests' => $guests,
        ]);
    }
}
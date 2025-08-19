<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PaymenttermsController extends AbstractController
{
    #[Route('/paymentterms', name: 'app_paymentterms')]
    public function index(): Response
    {
        return $this->render('paymentterms/index.html.twig', [
            'controller_name' => 'PaymenttermsController',
        ]);
    }
}

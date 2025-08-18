<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Booking;
use App\Entity\CarteBancaire;
use App\Entity\PayPal;
use App\Entity\Virement;
use App\Repository\BookingRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

final class PaymentController extends AbstractController
{
    #[Route('/payment', name: 'app_payment')]
    public function index(): Response
    {
        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }

    #[Route('/payment/booking/{id}', name: 'app_payment_booking', methods: ['GET'])]
    public function booking(Booking $booking, BookingRepository $bookingRepository): Response
    {
        // Check if the booking exists  
        if (!$booking) {
            throw $this->createNotFoundException('Booking not found');
        }
        
        // Check if the booking is already paid
        if ($booking->isPaid()) {
            $this->addFlash('info', 'Cette réservation a déjà été payée.');
            return $this->redirectToRoute('app_user_bookings_journal');
        }
        
        // Check if the booking belongs to the current user
        $user = $this->getUser();
        
        // Render the payment page with the booking details
        return $this->render('payment/index.html.twig', [
            'booking' => $booking,
            'user' => $user,
        ]);
    }

    #[Route('/api/payment/booking/{id}', name: 'api_payment_booking', methods: ['POST'])]
    public function processPaymentAjax(
        Request $request,
        Booking $booking,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        try {
            // Check if the booking exists and is not already paid
            if (!$booking) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Réservation introuvable.'
                ], 404);
            }
            
            if ($booking->isPaid()) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Cette réservation a déjà été payée.'
                ], 400);
            }

            // Get JSON payload
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Données invalides.',
                    'errors' => ['format' => 'Format JSON requis']
                ], 400);
            }

            // Validate CSRF token
            if (!isset($data['_token']) || !$this->isCsrfTokenValid('payment_' . $booking->getId(), $data['_token'])) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Token de sécurité invalide. Veuillez réessayer.',
                    'errors' => ['token' => 'Token invalide']
                ], 403);
            }

            // Validate payment data
            $validationResult = $this->validatePaymentData($data);
            if (!$validationResult['valid']) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Données de paiement invalides.',
                    'errors' => $validationResult['errors']
                ], 422);
            }
            
            // Start transaction
            $entityManager->beginTransaction();
            
            // Save payment details based on method
            $paymentMethod = $data['payment_method'];
            switch ($paymentMethod) {
                case 'card':
                    $this->saveCardPaymentFromData($data, $booking, $entityManager);
                    $successMessage = 'Paiement par carte bancaire effectué avec succès!';
                    break;
                    
                case 'paypal':
                    $this->savePayPalPaymentFromData($data, $booking, $entityManager);
                    $successMessage = 'Paiement PayPal effectué avec succès!';
                    break;
                    
                case 'bank':
                    $this->saveBankTransferPaymentFromData($data, $booking, $entityManager);
                    $successMessage = 'Demande de virement bancaire enregistrée avec succès! Vous recevrez les détails par email.';
                    break;
                    
                default:
                    throw new \InvalidArgumentException('Méthode de paiement invalide');
            }
            
            // Update booking payment status
            $booking->setPaid(true);
            $booking->setPaymentMethod($paymentMethod);
            $booking->setStatus('confirmed');
           
            $entityManager->persist($booking);
            $entityManager->flush();
            $entityManager->commit();
            
            return new JsonResponse([
                'success' => true,
                'message' => $successMessage . ' Votre réservation #' . $booking->getId() . ' est confirmée.',
                'booking_id' => $booking->getId(),
                'redirect_url' => $this->generateUrl('app_user_bookings_journal')
            ]);
            
        } catch (\Exception $e) {
            if ($entityManager->getConnection()->isTransactionActive()) {
                $entityManager->rollback();
            }
            
            //  debugging
            error_log('Payment processing error: ' . $e->getMessage());
            
            return new JsonResponse([
                'success' => false,
                'message' => 'Une erreur est survenue lors du traitement du paiement. Veuillez réessayer ou contacter le support.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function validatePaymentData(array $data): array
    {
        $errors = [];
        
        // Check if payment method is provided
        if (!isset($data['payment_method']) || empty($data['payment_method'])) {
            $errors['payment_method'] = 'Méthode de paiement requise.';
            return ['valid' => false, 'errors' => $errors];
        }

        $paymentMethod = $data['payment_method'];
        
        // Common required fields
        $requiredCommonFields = [
            'billing_address' => 'Adresse de facturation',
            'billing_city' => 'Ville',
            'billing_zip' => 'Code postal',
            'billing_country' => 'Pays'
        ];
        
        foreach ($requiredCommonFields as $field => $label) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $errors[$field] = "Le champ '$label' est obligatoire.";
            }
        }
        
        // Check terms acceptance
        if (!isset($data['terms']) || !$data['terms']) {
            $errors['terms'] = 'Vous devez accepter les conditions générales pour continuer.';
        }
        
        // Payment method specific validation
        switch ($paymentMethod) {
            case 'card':
                $cardFields = [
                    'card_holder' => 'Nom du titulaire',
                    'card_number' => 'Numéro de carte',
                    'expiry_date' => 'Date d\'expiration',
                    'cvv' => 'Code CVV'
                ];
                
                foreach ($cardFields as $field => $label) {
                    if (!isset($data[$field]) || empty(trim($data[$field]))) {
                        $errors[$field] = "Le champ '$label' est obligatoire.";
                    }
                }
                
                // Additional card validation
                if (isset($data['card_number'])) {
                    $cardNumber = str_replace(' ', '', $data['card_number']);
                    if (strlen($cardNumber) < 13 || strlen($cardNumber) > 19 || !ctype_digit($cardNumber)) {
                        $errors['card_number'] = 'Numéro de carte bancaire invalide.';
                    }
                }
                
                if (isset($data['expiry_date'])) {
                    if (!preg_match('/^\d{2}\/\d{2}$/', $data['expiry_date'])) {
                        $errors['expiry_date'] = 'Format de date d\'expiration invalide (MM/AA).';
                    }
                }
                
                if (isset($data['cvv'])) {
                    $cvv = $data['cvv'];
                    if (strlen($cvv) < 3 || strlen($cvv) > 4 || !ctype_digit($cvv)) {
                        $errors['cvv'] = 'Code CVV invalide.';
                    }
                }
                break;
                
            case 'paypal':
                if (!isset($data['paypal_email']) || empty(trim($data['paypal_email']))) {
                    $errors['paypal_email'] = 'Adresse email PayPal requise.';
                } elseif (!filter_var(trim($data['paypal_email']), FILTER_VALIDATE_EMAIL)) {
                    $errors['paypal_email'] = 'Adresse email PayPal invalide.';
                }
                break;
                
            case 'bank':
                // For bank transfer only common fields are required
                break;
                
            default:
                $errors['payment_method'] = 'Méthode de paiement invalide.';
                break;
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    private function saveCardPaymentFromData(array $data, Booking $booking, EntityManagerInterface $entityManager): void
    {
        try {
            $carteBancaire = new CarteBancaire();
            $carteBancaire->setBookingId($booking);
            $carteBancaire->setNomDuTitulaire(trim($data['card_holder']));
            
            // Mask card number for security
            $carteBancaire->setNumeroDeCarte($this->maskCardNumber($data['card_number']));
            $carteBancaire->setDateDexpiration($data['expiry_date']);
            
            // Never store CVV  mask it
            $carteBancaire->setCodeCvv('***');
            
            $carteBancaire->setAdresse(trim($data['billing_address']));
            $carteBancaire->setVille(trim($data['billing_city']));
            $carteBancaire->setCodePostal(trim($data['billing_zip']));
            $carteBancaire->setPays($data['billing_country']);
            
            $entityManager->persist($carteBancaire);
            
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de l\'enregistrement des informations de carte bancaire: ' . $e->getMessage());
        }
    }

    private function savePayPalPaymentFromData(array $data, Booking $booking, EntityManagerInterface $entityManager): void
    {  
        try {
            $paypal = new PayPal();
            $paypal->setBookingID($booking);
            $paypal->setEmailpaypal(trim($data['paypal_email']));
            $paypal->setAdresseDeFacturation(trim($data['billing_address']));
            $paypal->setVille(trim($data['billing_city']));
            $paypal->setCodePostal(trim($data['billing_zip']));
            $paypal->setPays($data['billing_country']);
            
            $entityManager->persist($paypal);
            
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de l\'enregistrement des informations PayPal: ' . $e->getMessage());
        }
    }
    
    private function saveBankTransferPaymentFromData(array $data, Booking $booking, EntityManagerInterface $entityManager): void
    {
        try {
            $virement = new Virement();
            $virement->setBookingId($booking);
            $virement->setAdresse(trim($data['billing_address']));
            $virement->setVille(trim($data['billing_city']));
            $virement->setCodePostal(trim($data['billing_zip']));
            $virement->setPays($data['billing_country']);
            
            $entityManager->persist($virement);
            
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de l\'enregistrement des informations de virement: ' . $e->getMessage());
        }
    }

   
    private function validatePaymentFields(Request $request, string $paymentMethod): ?string
    {
        // Common required fields
        $requiredCommonFields = ['billing_address', 'billing_city', 'billing_zip', 'billing_country'];
        
        foreach ($requiredCommonFields as $field) {
            if (empty($request->request->get($field))) {
                return "Le champ '" . $this->getFieldLabel($field) . "' est obligatoire.";
            }
        }
        
        // Check terms acceptance
        if (!$request->request->get('terms')) {
            return 'Vous devez accepter les conditions générales pour continuer.';
        }
        
        // Payment method specific validation
        switch ($paymentMethod) {
            case 'card':
                $cardFields = ['card_holder', 'card_number', 'expiry_date', 'cvv'];
                foreach ($cardFields as $field) {
                    $value = trim($request->request->get($field));
                    if (empty($value)) {
                        return "Le champ '" . $this->getFieldLabel($field) . "' est obligatoire.";
                    }
                }
                
                // Additional card validation
                $cardNumber = str_replace(' ', '', $request->request->get('card_number'));
                if (strlen($cardNumber) < 13 || strlen($cardNumber) > 19 || !ctype_digit($cardNumber)) {
                    return 'Numéro de carte bancaire invalide.';
                }
                
                $expiryDate = $request->request->get('expiry_date');
                if (!preg_match('/^\d{2}\/\d{2}$/', $expiryDate)) {
                    return 'Format de date d\'expiration invalide (MM/AA).';
                }
                
                $cvv = $request->request->get('cvv');
                if (strlen($cvv) < 3 || strlen($cvv) > 4 || !ctype_digit($cvv)) {
                    return 'Code CVV invalide.';
                }
                break;
                
            case 'paypal':
                $email = trim($request->request->get('paypal_email'));
                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return 'Adresse email PayPal invalide.';
                }
                break;
                
            case 'bank':
                // For bank transfer only common fields are required
                break;
        }
        
        return null;
    }
    
    private function getFieldLabel(string $field): string
    {
        $labels = [
            'billing_address' => 'Adresse de facturation',
            'billing_city' => 'Ville',
            'billing_zip' => 'Code postal',
            'billing_country' => 'Pays',
            'card_holder' => 'Nom du titulaire',
            'card_number' => 'Numéro de carte',
            'expiry_date' => 'Date d\'expiration',
            'cvv' => 'Code CVV',
            'paypal_email' => 'Email PayPal'
        ];
        
        return $labels[$field] ?? $field;
    }

    private function saveCardPayment(Request $request, Booking $booking, EntityManagerInterface $entityManager): void
    {
        try {
            $carteBancaire = new CarteBancaire();
            $carteBancaire->setBookingId($booking);
            $carteBancaire->setNomDuTitulaire(trim($request->request->get('card_holder')));
            
            // Mask card number for security
            $carteBancaire->setNumeroDeCarte($this->maskCardNumber($request->request->get('card_number')));
            $carteBancaire->setDateDexpiration($request->request->get('expiry_date'));
            
            // Never store CVV - mask it
            $carteBancaire->setCodeCvv('***');
            
            $carteBancaire->setAdresse(trim($request->request->get('billing_address')));
            $carteBancaire->setVille(trim($request->request->get('billing_city')));
            $carteBancaire->setCodePostal(trim($request->request->get('billing_zip')));
            $carteBancaire->setPays($request->request->get('billing_country'));
            
            $entityManager->persist($carteBancaire);
            
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de l\'enregistrement des informations de carte bancaire: ' . $e->getMessage());
        }
    }

    private function savePayPalPayment(Request $request, Booking $booking, EntityManagerInterface $entityManager): void
    {  
        try {
            $paypal = new PayPal();
            $paypal->setBookingID($booking);
            $paypal->setEmailpaypal(trim($request->request->get('paypal_email')));
            $paypal->setAdresseDeFacturation(trim($request->request->get('billing_address')));
            $paypal->setVille(trim($request->request->get('billing_city')));
            $paypal->setCodePostal(trim($request->request->get('billing_zip')));
            $paypal->setPays($request->request->get('billing_country'));
            
            $entityManager->persist($paypal);
            
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de l\'enregistrement des informations PayPal: ' . $e->getMessage());
        }
    }
    
    private function saveBankTransferPayment(Request $request, Booking $booking, EntityManagerInterface $entityManager): void
    {
        try {
            $virement = new Virement();
            $virement->setBookingId($booking);
            $virement->setAdresse(trim($request->request->get('billing_address')));
            $virement->setVille(trim($request->request->get('billing_city')));
            $virement->setCodePostal(trim($request->request->get('billing_zip')));
            $virement->setPays($request->request->get('billing_country'));
            
            $entityManager->persist($virement);
            
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de l\'enregistrement des informations de virement: ' . $e->getMessage());
        }
    }

    private function maskCardNumber(string $cardNumber): string
    {
        // Remove spaces and mask all but last 4 digits
        $cleanNumber = str_replace(' ', '', $cardNumber);
        return '**** **** **** ' . substr($cleanNumber, -4);
    }
}
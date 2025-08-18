<?php


namespace App\Repository;

use App\Entity\CarteBancaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CarteBancaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarteBancaire::class);
    }

    /**
     * Find card payments by booking
     */
    public function findByBooking($booking): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.BookingId = :booking')
            ->setParameter('booking', $booking)
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

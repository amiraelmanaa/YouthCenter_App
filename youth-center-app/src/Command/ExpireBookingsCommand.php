<?php

namespace App\Command;

use App\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:expire-bookings',
    description: 'Decline bookings that were accepted more than 48h ago but not paid'
)]
class ExpireBookingsCommand extends Command
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $threshold = new \DateTimeImmutable('-48 hours');

        $bookings = $this->em->getRepository(Booking::class)
            ->createQueryBuilder('b')
            ->where('b.status = :status')
            ->andWhere('b.acceptedAt <= :threshold')
            ->andWhere('b.Paid = false OR b.Paid IS NULL')
            ->setParameter('status', 'accepted')
            ->setParameter('threshold', $threshold)
            ->getQuery()
            ->getResult();

        foreach ($bookings as $booking) {
            $booking->setStatus('declined');
            $output->writeln("Booking {$booking->getId()} declined due to non-payment.");
        }

        $this->em->flush();

        return Command::SUCCESS;
    }
}

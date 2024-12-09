<?php
namespace App\Service;

use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Membership;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PaymentService
{
    private EntityManagerInterface $entityManager;
    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function createPayment(int $userId, float $amount, \DateTimeInterface $paymentDate, string $status, int $membershipId): Payment
    {
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        $membership = $this->entityManager->getRepository(Membership::class)->find($membershipId);

        if (!$user) {
            throw new BadRequestHttpException('User not found');
        }

        if (!$membership) {
            throw new BadRequestHttpException('Membership not found');
        }

        $payment = new Payment();
        $payment->setUser($user);
        $payment->setAmount(number_format($amount, 2, '.', ''));
        $payment->setPaymentDate($paymentDate);
        $payment->setStatus($status);
        $payment->setMembership($membership);

        $this->entityManager->persist($payment);
        $this->entityManager->flush();

        return $payment;
    }
}

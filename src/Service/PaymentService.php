<?php
namespace App\Service;

use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Membership;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Services\RequestCheckerService;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentService
{
    private EntityManagerInterface $entityManager;
    private Security $security;
    private RequestCheckerService $requestChecker;

    public function __construct(EntityManagerInterface $entityManager, Security $security, RequestCheckerService $requestChecker
)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->requestChecker = $requestChecker;
    }

    public function createPayment(int $userId, float $amount, \DateTimeInterface $paymentDate, string $status, int $membershipId): Payment
    {

        $this->requestChecker->check([
            'userId' => $userId,
            'amount' => $amount,
            'paymentDate' => $paymentDate,
            'status' => $status,
            'membershipId' => $membershipId
        ], ['userId', 'amount', 'paymentDate', 'status', 'membershipId']);

        if ($amount <= 0) {
            throw new BadRequestHttpException('Amount must be positive');
        }

        $validStatuses = ['pending', 'completed', 'failed'];
        if (!in_array($status, $validStatuses)) {
            throw new BadRequestHttpException('Invalid payment status');
        }


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

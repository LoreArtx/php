<?php

namespace App\Service;

use App\Entity\Membership;
use App\Entity\Payment;
use App\Entity\User;
use App\Entity\SubscriptionPlan;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Services\RequestCheckerService;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MembershipService
{
    private EntityManagerInterface $entityManager;
    private RequestCheckerService $requestChecker;

    public function __construct(EntityManagerInterface $entityManager, RequestCheckerService $requestChecker
)
    {
        $this->entityManager = $entityManager;
        $this->requestChecker = $requestChecker;
    }

    public function createMembership(int $userId, int $subscriptionPlanId, \DateTimeInterface $startDate, \DateTimeInterface $endDate): Membership
    {
        $this->requestChecker->check([
            'userId' => $userId,
            'subscriptionPlanId' => $subscriptionPlanId,
            'startDate' => $startDate,
            'endDate' => $endDate
        ], ['userId', 'subscriptionPlanId', 'startDate', 'endDate']);

        if ($startDate > $endDate) {
            throw new BadRequestHttpException('Start date must be before end date');
        }

        $user = $this->entityManager->getRepository(User::class)->find($userId);
        $subscriptionPlan = $this->entityManager->getRepository(SubscriptionPlan::class)->find($subscriptionPlanId);

        if (!$user) {
            throw new BadRequestHttpException('User not found');
        }

        if (!$subscriptionPlan) {
            throw new BadRequestHttpException('Subscription Plan not found');
        }

        $membership = new Membership();
        $membership->setUser($user);
        $membership->setSubscription($subscriptionPlan);
        $membership->setStartDate($startDate);
        $membership->setEndDate($endDate);

        $this->entityManager->persist($membership);
        $this->entityManager->flush();

        return $membership;
    }
}

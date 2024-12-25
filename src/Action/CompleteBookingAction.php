<?php

namespace App\Action;

use App\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CompleteBookingAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /*
     * @param int $id
     * @return JsonResponse
     */
    public function __invoke(int $id): JsonResponse
    {
        $booking = $this->entityManager->getRepository(Booking::class)->find($id);

        if (!$booking) {
            throw new NotFoundHttpException('Booking not found.');
        }

        if ($booking->getStatus() === 'confirmed') {
            throw new BadRequestHttpException('This booking has already been confirmed.');
        }

        $booking->setStatus('confirmed');
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'success', 'booking' => $booking], 200);
    }
}

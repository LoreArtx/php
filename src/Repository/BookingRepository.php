<?php

namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Booking>
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * @param array $data
     * @param int $itemsPerPage
     * @param int $page
     * @return array
     */
    public function getAllBookingsByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('b');

        if (isset($data['userId'])) {
            $queryBuilder->andWhere('b.user = :userId')
                ->setParameter('userId', $data['userId']);
        }

        if (isset($data['workoutSessionId'])) {
            $queryBuilder->andWhere('b.workoutSession = :workoutSessionId')
                ->setParameter('workoutSessionId', $data['workoutSessionId']);
        }

        if (isset($data['status'])) {
            $queryBuilder->andWhere('b.status = :status')
                ->setParameter('status', $data['status']);
        }

        $paginator = new Paginator($queryBuilder);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $itemsPerPage);

        $paginator
            ->getQuery()
            ->setFirstResult($itemsPerPage * ($page - 1))
            ->setMaxResults($itemsPerPage);

        return [
            'bookings' => $paginator->getQuery()->getResult(),
            'totalItems' => $totalItems,
            'totalPages' => $pagesCount,
        ];
    }
}

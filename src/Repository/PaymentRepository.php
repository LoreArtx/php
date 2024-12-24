<?php

namespace App\Repository;

use App\Entity\Payment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Payment>
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    /**
     * @param array $data
     * @param int $itemsPerPage
     * @param int $page
     * @return array
     */
    public function getAllPaymentByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('p');

        // Фільтрація за полями
        if (isset($data['user'])) {
            $queryBuilder->andWhere('p.user = :user')
                ->setParameter('user', $data['user']);
        }

        if (isset($data['membership'])) {
            $queryBuilder->andWhere('p.membership = :membership')
                ->setParameter('membership', $data['membership']);
        }

        if (isset($data['start_date'])) {
            $queryBuilder->andWhere('p.paymentDate >= :start_date')
                ->setParameter('start_date', $data['start_date']);
        }

        if (isset($data['end_date'])) {
            $queryBuilder->andWhere('p.paymentDate <= :end_date')
                ->setParameter('end_date', $data['end_date']);
        }

        $paginator = new Paginator($queryBuilder);
        $totalItems = count($paginator);
        $totalPages = ceil($totalItems / $itemsPerPage);

        $paginator
            ->getQuery()
            ->setFirstResult($itemsPerPage * ($page - 1))
            ->setMaxResults($itemsPerPage);

        return [
            'payments' => $paginator->getQuery()->getResult(),
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
        ];
    }
}

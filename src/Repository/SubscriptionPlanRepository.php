<?php

namespace App\Repository;

use App\Entity\SubscriptionPlan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<SubscriptionPlan>
 */
class SubscriptionPlanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubscriptionPlan::class);
    }

    /**
     * @param int $itemsPerPage
     * @param int $page
     * @return array
     */
    public function getAllSubscriptionPlans(int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('s');

        $paginator = new Paginator($queryBuilder);
        $totalItems = count($paginator);
        $totalPages = ceil($totalItems / $itemsPerPage);

        $paginator
            ->getQuery()
            ->setFirstResult($itemsPerPage * ($page - 1))
            ->setMaxResults($itemsPerPage);

        return [
            'subscriptionPlans' => $paginator->getQuery()->getResult(),
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
        ];
    }
}

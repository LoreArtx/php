<?php

namespace App\Repository;

use App\Entity\Membership;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

class MembershipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Membership::class);
    }

    /**
     * @param array $data
     * @param int $itemsPerPage
     * @param int $page
     * @return array
     */
    public function getAllMembershipByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('m');

        if (isset($data['user'])) {
            $queryBuilder->andWhere('m.user = :user')
                ->setParameter('user', $data['user']);
        }

        if (isset($data['subscription_plan'])) {
            $queryBuilder->andWhere('m.subscription = :subscription_plan')
                ->setParameter('subscription_plan', $data['subscription_plan']);
        }

        if (isset($data['start_date'])) {
            $queryBuilder->andWhere('m.startDate >= :start_date')
                ->setParameter('start_date', $data['start_date']);
        }

        if (isset($data['end_date'])) {
            $queryBuilder->andWhere('m.endDate <= :end_date')
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
            'memberships' => $paginator->getQuery()->getResult(),
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
        ];
    }
}

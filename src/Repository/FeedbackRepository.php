<?php

namespace App\Repository;

use App\Entity\Feedback;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

class FeedbackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feedback::class);
    }

    /**
     * @param array $data
     * @param int $itemsPerPage
     * @param int $page
     * @return array
     */
    public function getAllFeedbackByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('f');

        if (isset($data['user'])) {
            $queryBuilder->andWhere('f.user = :user')
                ->setParameter('user', $data['user']);
        }

        if (isset($data['trainer'])) {
            $queryBuilder->andWhere('f.trainer = :trainer')
                ->setParameter('trainer', $data['trainer']);
        }

        if (isset($data['rating'])) {
            $queryBuilder->andWhere('f.rating = :rating')
                ->setParameter('rating', $data['rating']);
        }

        if (isset($data['comment'])) {
            $queryBuilder->andWhere('f.comment LIKE :comment')
                ->setParameter('comment', '%' . $data['comment'] . '%');
        }

        $paginator = new Paginator($queryBuilder);
        $totalItems = count($paginator);
        $totalPages = ceil($totalItems / $itemsPerPage);

        $paginator
            ->getQuery()
            ->setFirstResult($itemsPerPage * ($page - 1))
            ->setMaxResults($itemsPerPage);

        return [
            'feedback' => $paginator->getQuery()->getResult(),
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
        ];
    }
}

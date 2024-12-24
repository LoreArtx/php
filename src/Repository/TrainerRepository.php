<?php

namespace App\Repository;

use App\Entity\Trainer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Trainer>
 */
class TrainerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trainer::class);
    }

    /**
     * @param int $itemsPerPage
     * @param int $page
     * @return array
     */
    public function getTrainersPaginated(int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('t');

        $paginator = new Paginator($queryBuilder);
        $totalItems = count($paginator);
        $totalPages = ceil($totalItems / $itemsPerPage);

        $paginator
            ->getQuery()
            ->setFirstResult($itemsPerPage * ($page - 1))
            ->setMaxResults($itemsPerPage);

        return [
            'trainers' => $paginator->getQuery()->getResult(),
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
        ];
    }
}

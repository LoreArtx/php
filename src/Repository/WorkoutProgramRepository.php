<?php

namespace App\Repository;

use App\Entity\WorkoutProgram;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<WorkoutProgram>
 */
class WorkoutProgramRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkoutProgram::class);
    }

    /**
     * @param int $itemsPerPage
     * @param int $page
     * @return array
     */
    public function getWorkoutProgramsPaginated(int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('w');

        $paginator = new Paginator($queryBuilder);
        $totalItems = count($paginator);
        $totalPages = ceil($totalItems / $itemsPerPage);

        $paginator
            ->getQuery()
            ->setFirstResult($itemsPerPage * ($page - 1))
            ->setMaxResults($itemsPerPage);

        return [
            'workoutPrograms' => $paginator->getQuery()->getResult(),
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
        ];
    }
}

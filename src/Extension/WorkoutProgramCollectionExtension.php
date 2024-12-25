<?php

declare(strict_types=1);

namespace App\Extension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

class WorkoutProgramCollectionExtension implements QueryCollectionExtensionInterface
{
    private const ADMIN_ROLES = ['ROLE_ADMIN'];

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * {@inheritdoc}
     */
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        if ($this->security->isGranted(self::ADMIN_ROLES)) {
            return;
        }

        // Non-admins can see only the programs related to their trainer
        $this->addTrainerFilter($queryBuilder);
    }

    /**
     * Add a filter to the query builder to fetch only programs for the logged-in trainer.
     */
    private function addTrainerFilter(QueryBuilder $queryBuilder): void
    {
        $trainer = $this->security->getUser()->getTrainer();
        if ($trainer) {
            $queryBuilder->andWhere('o.trainer = :trainer')
                         ->setParameter('trainer', $trainer);
        }
    }
}

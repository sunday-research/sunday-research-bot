<?php

declare(strict_types=1);

namespace App\Module\BotGetUpdates\Infrastructure\Doctrine\Repository;

use App\Module\BotGetUpdates\Infrastructure\Doctrine\Model\Issue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Issue>
 */
class IssueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Issue::class);
    }

    public function create(Issue $doctrineIssue): void
    {
        $this->getEntityManager()->persist($doctrineIssue);
        $this->getEntityManager()->flush();
    }
}

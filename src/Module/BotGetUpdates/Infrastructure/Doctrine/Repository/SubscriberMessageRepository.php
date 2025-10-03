<?php

declare(strict_types=1);

namespace App\Module\BotGetUpdates\Infrastructure\Doctrine\Repository;

use App\Module\BotGetUpdates\Infrastructure\Doctrine\Model\SubscriberMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SubscriberMessage>
 *
 * @method SubscriberMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubscriberMessage|null findOneBy(array<string, mixed> $criteria, array<string, string> $orderBy = null)
 * @method SubscriberMessage[]    findAll()
 * @method SubscriberMessage[]    findBy(array<string, mixed> $criteria, array<string, string> $orderBy = null, $limit = null, $offset = null)
 */
class SubscriberMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubscriberMessage::class);
    }

    public function create(SubscriberMessage $subscriberMessage): void
    {
        $this->getEntityManager()->persist($subscriberMessage);
        $this->getEntityManager()->flush();
    }

    public function update(SubscriberMessage $subscriberMessage): void
    {
        $this->getEntityManager()->persist($subscriberMessage);
        $this->getEntityManager()->flush();
    }
}

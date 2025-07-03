<?php

namespace App\Bundle\BotGetUpdates\Infrastructure\Doctrine\Repository;

use App\Bundle\BotGetUpdates\Infrastructure\Doctrine\Model\SubscriberMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SubscriberMessage>
 *
 * @method SubscriberMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubscriberMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubscriberMessage[]    findAll()
 * @method SubscriberMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
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
}

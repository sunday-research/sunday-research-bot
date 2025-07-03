<?php

namespace App\Bundle\BotGetUpdates\Infrastructure\Doctrine\Repository;

use App\Bundle\BotGetUpdates\Infrastructure\Doctrine\Model\Subscriber;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subscriber>
 *
 * @method Subscriber|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscriber|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscriber[]    findAll()
 * @method Subscriber[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscriber::class);
    }

    public function findByTelegramUserId(int $telegramUserId): ?Subscriber
    {
        return $this->findOneBy(['telegramUserId' => $telegramUserId]);
    }

    public function create(Subscriber $subscriber): void
    {
        $existingSubscriber = $this->findByTelegramUserId($subscriber->getTelegramUserId());
        if ($existingSubscriber) {
            return;
        }

        $this->getEntityManager()->persist($subscriber);
        $this->getEntityManager()->flush();
    }
}

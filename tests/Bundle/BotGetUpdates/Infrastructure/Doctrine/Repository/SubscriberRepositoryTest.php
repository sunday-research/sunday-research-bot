<?php

declare(strict_types=1);

namespace App\Tests\Bundle\BotGetUpdates\Infrastructure\Doctrine\Repository;

use App\Bundle\BotGetUpdates\Infrastructure\Doctrine\Model\Subscriber;
use App\Bundle\BotGetUpdates\Infrastructure\Doctrine\Repository\SubscriberRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SubscriberRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private SubscriberRepository $repository;

    /** @var array<string> */
    protected array $createdSubscriberIds = [];

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->em = $container->get(EntityManagerInterface::class);
        /** @var SubscriberRepository $repository */
        $repository = $this->em->getRepository(Subscriber::class);
        $this->repository = $repository;
    }

    public function testCreateSubscriber(): void
    {
        $subscriber = $this->createAndPersistSubscriber();
        $this->assertNotNull($subscriber->getId());
    }

    public function testReadSubscriber(): void
    {
        $subscriber = $this->createAndPersistSubscriber();
        $found = $this->repository->find($subscriber->getId());
        $this->assertNotNull($found);
        $this->assertEquals('Ivan', $found->getFirstName());
    }

    public function testUpdateSubscriber(): void
    {
        $subscriber = $this->createAndPersistSubscriber();
        $subscriber->setLastName('Petrov');
        $this->em->flush();

        $updated = $this->repository->find($subscriber->getId());
        $this->assertEquals('Petrov', $updated->getLastName());
    }

    public function testDeleteSubscriber(): void
    {
        $subscriber = $this->createAndPersistSubscriber();
        $id = $subscriber->getId();

        $this->em->remove($subscriber);
        $this->em->flush();

        $deleted = $this->repository->find($id);
        $this->assertNull($deleted);
    }

    private function createAndPersistSubscriber(): Subscriber
    {
        $subscriber = new Subscriber();
        $subscriber->setTelegramUserId(123456)
            ->setFirstName('Ivan')
            ->setUsername('ivanuser')
            ->setLastName('Ivanov')
            ->setLanguageCode('ru')
            ->setIsPremium(true);

        $this->em->persist($subscriber);
        $this->em->flush();
        $this->createdSubscriberIds[] = $subscriber->getId()?->toString();

        return $subscriber;
    }

    /**
     * @throws Exception
     */
    protected function tearDown(): void
    {
        $connection = $this->em->getConnection();

        if (!empty($this->createdSubscriberIds)) {
            $ids = array_map(fn($id) => $connection->quote($id), $this->createdSubscriberIds);
            $connection->executeStatement('DELETE FROM subscriber WHERE id IN (' . implode(',', $ids) . ')');
        }

        $this->em->close();
        parent::tearDown();
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Module\BotGetUpdates\Infrastructure\Doctrine\Repository;

use App\Module\BotGetUpdates\Infrastructure\Doctrine\Model\Subscriber;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Model\SubscriberMessage;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Repository\SubscriberMessageRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Throwable;

class SubscriberMessageRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private SubscriberMessageRepository $repository;

    /** @var array<string> */
    protected array $createdSubscriberIds = [];
    /** @var array<string> */
    protected array $createdMessageIds = [];

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        /** @phpstan-ignore-next-line mixed.type */
        $this->em = $container->get(EntityManagerInterface::class);
        /** @var SubscriberMessageRepository $repository */
        $repository = $this->em->getRepository(SubscriberMessage::class);
        $this->repository = $repository;
    }

    public function testCreateSubscriberMessage(): void
    {
        $subscriber = $this->createAndPersistSubscriber();
        $message = $this->createAndPersistMessage($subscriber);

        $this->assertNotNull($message->getId());
        $this->assertEquals($subscriber->getId(), $message->getSubscriber()->getId());
    }

    public function testUpdateSubscriberMessage(): void
    {
        $subscriber = $this->createAndPersistSubscriber();
        $message = $this->createAndPersistMessage($subscriber);

        $message->setMessageText('Updated text');
        $this->em->flush();

        $updated = $this->repository->find($message->getId());
        $this->assertEquals('Updated text', $updated?->getMessageText());
    }

    public function testDeleteSubscriberMessage(): void
    {
        $subscriber = $this->createAndPersistSubscriber();
        $message = $this->createAndPersistMessage($subscriber);
        $id = $message->getId();

        $this->em->remove($message);
        $this->em->flush();

        $deleted = $this->repository->find($id);
        $this->assertNull($deleted);
    }

    public function testSubscriberRelation(): void
    {
        $subscriber = $this->createAndPersistSubscriber();
        $message = $this->createAndPersistMessage($subscriber);

        $foundSubscriber = $message->getSubscriber();
        $this->assertEquals($subscriber->getId(), $foundSubscriber->getId());
    }

    public function testPersistNewSubscriberAndMessageWithSingleFlush(): void
    {
        $telegramUserId = 123456;
        try {
            $telegramUserId = random_int(100000, 999999);
        } catch (Throwable) {}

        $subscriber = new Subscriber();
        $subscriber->setTelegramUserId($telegramUserId)
            ->setFirstName('Ivan')
            ->setUsername('ivanuser')
            ->setLastName('Ivanov')
            ->setLanguageCode('ru')
            ->setIsPremium(true);
        $this->em->persist($subscriber);

        $message = new SubscriberMessage();
        $message->setSubscriber($subscriber)
            ->setMessageText('Test message')
            ->setMessageDate(new DateTimeImmutable())
            ->setChatId(12345)
            ->setMessageId(1)
            ->setIsBotSender(false);
        $this->em->persist($message);

        $this->em->flush();

        /** @phpstan-ignore-next-line mixed.type */
        $this->createdSubscriberIds[] = $subscriber->getId()?->toString();
        /** @phpstan-ignore-next-line mixed.type */
        $this->createdMessageIds[] = $message->getId()?->toString();

        $this->assertNotNull($subscriber->getId());
        $this->assertNotNull($message->getId());
        $this->assertEquals($subscriber->getId(), $message->getSubscriber()->getId());
    }

    private function createAndPersistSubscriber(): Subscriber
    {
        $telegramUserId = 123456;
        try {
            $telegramUserId = random_int(100000, 999999);
        } catch (Throwable) {}

        $subscriber = new Subscriber();
        $subscriber->setTelegramUserId($telegramUserId)
            ->setFirstName('Ivan')
            ->setUsername('ivanuser')
            ->setLastName('Ivanov')
            ->setLanguageCode('ru')
            ->setIsPremium(true);

        $this->em->persist($subscriber);
        $this->em->flush();
        /** @phpstan-ignore-next-line mixed.type */
        $this->createdSubscriberIds[] = $subscriber->getId()?->toString();

        return $subscriber;
    }

    private function createAndPersistMessage(Subscriber $subscriber): SubscriberMessage
    {
        $message = new SubscriberMessage();
        $message->setSubscriber($subscriber)
            ->setMessageText('Test message')
            ->setMessageDate(new DateTimeImmutable())
            ->setChatId(12345)
            ->setMessageId(1)
            ->setIsBotSender(false);

        $this->em->persist($message);
        $this->em->flush();
        $this->createdMessageIds[] = (string)$message->getId();

        return $message;
    }

    /**
     * @throws Exception
     */
    protected function tearDown(): void
    {
        $connection = $this->em->getConnection();

        if (!empty($this->createdMessageIds)) {
            $ids = array_map(fn($id) => $connection->quote($id), $this->createdMessageIds);
            $connection->executeStatement('DELETE FROM subscriber_messages WHERE id IN (' . implode(',', $ids) . ')');
        }

        if (!empty($this->createdSubscriberIds)) {
            $ids = array_map(fn($id) => $connection->quote($id), $this->createdSubscriberIds);
            $connection->executeStatement('DELETE FROM subscriber WHERE id IN (' . implode(',', $ids) . ')');
        }

        $this->em->close();
        parent::tearDown();
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Bundle\BotGetUpdates\Infrastructure\Doctrine\Builder;

use App\Bundle\BotGetUpdates\Entity\SubscriberMessage as DomainSubscriberMessage;
use App\Bundle\BotGetUpdates\Infrastructure\Doctrine\Builder\SubscriberMessageBuilder;
use App\Bundle\BotGetUpdates\Infrastructure\Doctrine\Model\SubscriberMessage as DoctrineSubscriberMessage;
use App\Bundle\BotGetUpdates\Infrastructure\Doctrine\Model\Subscriber as DoctrineSubscriber;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use DateTimeImmutable;

class SubscriberMessageBuilderTest extends TestCase
{
    private function createDoctrineSubscriber($uuid): DoctrineSubscriber
    {
        $subscriber = $this->createMock(DoctrineSubscriber::class);
        $subscriber->method('getId')->willReturn($uuid);

        return $subscriber;
    }

    public function testBuildReturnsDomainSubscriberMessageWithCorrectData(): void
    {
        $uuid = Uuid::uuid7();
        $subscriberUuid = Uuid::uuid7();
        $chatId = 123456789;
        $messageId = 42;
        $messageText = 'Test message';
        $messageDate = new DateTimeImmutable('2024-06-01 12:00:00');
        $isBotSender = true;

        $doctrineSubscriber = $this->createDoctrineSubscriber($subscriberUuid);

        $doctrineMessage = $this->createMock(DoctrineSubscriberMessage::class);
        $doctrineMessage->method('getId')->willReturn($uuid);
        $doctrineMessage->method('getSubscriber')->willReturn($doctrineSubscriber);
        $doctrineMessage->method('getChatId')->willReturn($chatId);
        $doctrineMessage->method('getMessageId')->willReturn($messageId);
        $doctrineMessage->method('getMessageText')->willReturn($messageText);
        $doctrineMessage->method('getMessageDate')->willReturn($messageDate);
        $doctrineMessage->method('isBotSender')->willReturn($isBotSender);

        $domainMessage = SubscriberMessageBuilder::build($doctrineMessage);

        $this->assertInstanceOf(DomainSubscriberMessage::class, $domainMessage);
        $this->assertEquals($uuid->toString(), $domainMessage->getId());
        $this->assertEquals($subscriberUuid->toString(), $domainMessage->getSubscriberId());
        $this->assertEquals($chatId, $domainMessage->getChatId());
        $this->assertEquals($messageId, $domainMessage->getMessageId());
        $this->assertEquals($messageText, $domainMessage->getMessageText());
        $this->assertEquals($messageDate, $domainMessage->getMessageDate());
        $this->assertTrue($domainMessage->isBotSender());
    }

    public function testBuildWithNullIds(): void
    {
        $doctrineSubscriber = $this->createDoctrineSubscriber(null);

        $doctrineMessage = $this->createMock(DoctrineSubscriberMessage::class);
        $doctrineMessage->method('getId')->willReturn(null);
        $doctrineMessage->method('getSubscriber')->willReturn($doctrineSubscriber);
        $doctrineMessage->method('getChatId')->willReturn(1);
        $doctrineMessage->method('getMessageId')->willReturn(2);
        $doctrineMessage->method('getMessageText')->willReturn('text');
        $doctrineMessage->method('getMessageDate')->willReturn(new \DateTimeImmutable());
        $doctrineMessage->method('isBotSender')->willReturn(false);

        $this->expectException(\TypeError::class);

        SubscriberMessageBuilder::build($doctrineMessage);
    }
}

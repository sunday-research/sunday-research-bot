<?php

declare(strict_types=1);

namespace App\Tests\Module\BotGetUpdates\Infrastructure\Doctrine\Mapper;

use App\Module\BotGetUpdates\Entity\SubscriberMessage as DomainSubscriberMessage;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Mapper\SubscriberMessageMapper;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Model\Subscriber as DoctrineSubscriber;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Model\SubscriberMessage as DoctrineSubscriberMessage;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class SubscriberMessageMapperTest extends TestCase
{
    private function createDomainMessage(): DomainSubscriberMessage
    {
        return new DomainSubscriberMessage(
            Uuid::uuid7()->toString(),
            Uuid::uuid7()->toString(),
            123,
            456,
            'test text',
            new DateTimeImmutable('2024-06-01 12:00:00'),
            true
        );
    }

    private function createDoctrineSubscriber(): DoctrineSubscriber
    {
        return $this->createMock(DoctrineSubscriber::class);
    }

    public function testMapCreatesNewDoctrineSubscriberMessage(): void
    {
        $domainMessage = $this->createDomainMessage();
        $doctrineSubscriber = $this->createDoctrineSubscriber();

        $result = SubscriberMessageMapper::map($domainMessage, $doctrineSubscriber);

        $this->assertInstanceOf(DoctrineSubscriberMessage::class, $result);
        $this->assertSame($doctrineSubscriber, $result->getSubscriber());
        $this->assertEquals($domainMessage->getChatId(), $result->getChatId());
        $this->assertEquals($domainMessage->getMessageId(), $result->getMessageId());
        $this->assertEquals($domainMessage->getMessageText(), $result->getMessageText());
        $this->assertEquals($domainMessage->getMessageDate(), $result->getMessageDate());
        $this->assertEquals($domainMessage->isBotSender(), $result->isBotSender());
    }

    public function testMapUpdatesExistingDoctrineSubscriberMessage(): void
    {
        $domainMessage = $this->createDomainMessage();
        $doctrineSubscriber = $this->createDoctrineSubscriber();

        $existing = $this->createMock(DoctrineSubscriberMessage::class);

        $existing->expects($this->once())->method('setSubscriber')->with($doctrineSubscriber)->willReturnSelf();
        $existing->expects($this->once())->method('setChatId')->with($domainMessage->getChatId())->willReturnSelf();
        $existing->expects($this->once())->method('setMessageId')->with($domainMessage->getMessageId())->willReturnSelf();
        $existing->expects($this->once())->method('setMessageText')->with($domainMessage->getMessageText())->willReturnSelf();
        $existing->expects($this->once())->method('setMessageDate')->with($domainMessage->getMessageDate())->willReturnSelf();
        $existing->expects($this->once())->method('setIsBotSender')->with($domainMessage->isBotSender())->willReturnSelf();

        $result = SubscriberMessageMapper::map($domainMessage, $doctrineSubscriber, $existing);

        $this->assertSame($existing, $result);
    }
}

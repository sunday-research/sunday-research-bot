<?php

declare(strict_types=1);

namespace App\Tests\Module\BotGetUpdates\Infrastructure\Doctrine\Mapper;

use App\Module\BotGetUpdates\Entity\Subscriber as DomainSubscriber;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Mapper\SubscriberMapper;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Model\Subscriber as DoctrineSubscriber;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class SubscriberMapperTest extends TestCase
{
    private function createDomainSubscriber(): DomainSubscriber
    {
        return new DomainSubscriber(
            Uuid::uuid7()->toString(),
            17823456,
            'ivanuser',
            'Ivan',
            'Ivanov',
            'ru',
            true,
        );
    }

    public function testMapCreatesNewDoctrineSubscriber(): void
    {
        $domainSubscriber = $this->createDomainSubscriber();

        $result = SubscriberMapper::map($domainSubscriber);

        $this->assertInstanceOf(DoctrineSubscriber::class, $result);
        $this->assertEquals($domainSubscriber->getTelegramUserId(), $result->getTelegramUserId());
        $this->assertEquals($domainSubscriber->getUsername(), $result->getUsername());
        $this->assertEquals($domainSubscriber->getFirstName(), $result->getFirstName());
        $this->assertEquals($domainSubscriber->getLastName(), $result->getLastName());
        $this->assertEquals($domainSubscriber->getLanguageCode(), $result->getLanguageCode());
        $this->assertEquals($domainSubscriber->isPremium(), $result->isPremium());
    }

    public function testMapUpdatesExistingDoctrineSubscriber(): void
    {
        $domainSubscriber = $this->createDomainSubscriber();

        $existing = $this->createMock(DoctrineSubscriber::class);
        $existing->expects($this->once())->method('setTelegramUserId')->with($domainSubscriber->getTelegramUserId())->willReturnSelf();
        $existing->expects($this->once())->method('setUsername')->with($domainSubscriber->getUsername())->willReturnSelf();
        $existing->expects($this->once())->method('setFirstName')->with($domainSubscriber->getFirstName())->willReturnSelf();
        $existing->expects($this->once())->method('setLastName')->with($domainSubscriber->getLastName())->willReturnSelf();
        $existing->expects($this->once())->method('setLanguageCode')->with($domainSubscriber->getLanguageCode())->willReturnSelf();
        $existing->expects($this->once())->method('setIsPremium')->with($domainSubscriber->isPremium())->willReturnSelf();

        $result = SubscriberMapper::map($domainSubscriber, $existing);

        $this->assertSame($existing, $result);
    }
}

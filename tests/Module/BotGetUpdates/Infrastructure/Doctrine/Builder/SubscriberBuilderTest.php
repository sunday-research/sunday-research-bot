<?php

declare(strict_types=1);

namespace App\Tests\Module\BotGetUpdates\Infrastructure\Doctrine\Builder;

use App\Module\BotGetUpdates\Entity\Subscriber as DomainSubscriber;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Builder\SubscriberBuilder;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Model\Subscriber as DoctrineSubscriber;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use ReflectionClass;

class SubscriberBuilderTest extends TestCase
{
    private function setDoctrineSubscriberId(DoctrineSubscriber $subscriber, ?UuidInterface $id): void
    {
        $reflection = new ReflectionClass($subscriber);
        $property = $reflection->getProperty('id');
        $property->setValue($subscriber, $id);
    }

    public function testBuildReturnsDomainSubscriberWithCorrectData(): void
    {
        $doctrineSubscriber = new DoctrineSubscriber();
        $uuid = Uuid::uuid7();
        $this->setDoctrineSubscriberId($doctrineSubscriber, $uuid);
        $doctrineSubscriber->setTelegramUserId(123456)
            ->setFirstName('Ivan')
            ->setUsername('ivanuser')
            ->setLastName('Ivanov')
            ->setLanguageCode('ru')
            ->setIsPremium(true);

        $domainSubscriber = SubscriberBuilder::build($doctrineSubscriber);

        $this->assertInstanceOf(DomainSubscriber::class, $domainSubscriber);
        $this->assertEquals($uuid->toString(), $domainSubscriber->getId());
        $this->assertEquals(123456, $domainSubscriber->getTelegramUserId());
        $this->assertEquals('Ivan', $domainSubscriber->getFirstName());
        $this->assertEquals('ivanuser', $domainSubscriber->getUsername());
        $this->assertEquals('Ivanov', $domainSubscriber->getLastName());
        $this->assertEquals('ru', $domainSubscriber->getLanguageCode());
        $this->assertTrue($domainSubscriber->isPremium());
    }

    public function testBuildWithNullFields(): void
    {
        $this->expectException(\TypeError::class);

        $doctrineSubscriber = new DoctrineSubscriber();
        $this->setDoctrineSubscriberId($doctrineSubscriber, null);
        // Оставляем только валидные типы для остальных полей
        $doctrineSubscriber->setTelegramUserId(null)
            ->setFirstName(null)
            ->setUsername(null)
            ->setLastName(null)
            ->setLanguageCode(null)
            ->setIsPremium(false);

        $domainSubscriber = SubscriberBuilder::build($doctrineSubscriber);

        $this->assertNull($domainSubscriber->getId());
        $this->assertNull($domainSubscriber->getTelegramUserId());
        $this->assertNull($domainSubscriber->getFirstName());
        $this->assertNull($domainSubscriber->getUsername());
        $this->assertNull($domainSubscriber->getLastName());
        $this->assertNull($domainSubscriber->getLanguageCode());
        $this->assertFalse($domainSubscriber->isPremium());
    }

    public function testBuildWithPartialData(): void
    {
        $doctrineSubscriber = new DoctrineSubscriber();
        $uuid = Uuid::uuid7();
        $this->setDoctrineSubscriberId($doctrineSubscriber, $uuid);
        $doctrineSubscriber->setTelegramUserId(654321)
            ->setFirstName('Petr')
            ->setUsername(null)
            ->setLastName(null)
            ->setLanguageCode('en')
            ->setIsPremium(false);

        $domainSubscriber = SubscriberBuilder::build($doctrineSubscriber);

        $this->assertEquals($uuid->toString(), $domainSubscriber->getId());
        $this->assertEquals(654321, $domainSubscriber->getTelegramUserId());
        $this->assertEquals('Petr', $domainSubscriber->getFirstName());
        $this->assertNull($domainSubscriber->getUsername());
        $this->assertNull($domainSubscriber->getLastName());
        $this->assertEquals('en', $domainSubscriber->getLanguageCode());
        $this->assertFalse($domainSubscriber->isPremium());
    }

    public function testBuildWithIsPremiumTrueAndFalse(): void
    {
        $doctrineSubscriber = new DoctrineSubscriber();
        $uuid = Uuid::uuid7();
        $this->setDoctrineSubscriberId($doctrineSubscriber, $uuid);
        $doctrineSubscriber->setTelegramUserId(111111)
            ->setFirstName('Anna')
            ->setUsername('annatest')
            ->setLastName('Annova')
            ->setLanguageCode('de')
            ->setIsPremium(true);

        $domainSubscriber = SubscriberBuilder::build($doctrineSubscriber);
        $this->assertTrue($domainSubscriber->isPremium());

        $doctrineSubscriber->setIsPremium(false);
        $domainSubscriber = SubscriberBuilder::build($doctrineSubscriber);
        $this->assertFalse($domainSubscriber->isPremium());
    }
}

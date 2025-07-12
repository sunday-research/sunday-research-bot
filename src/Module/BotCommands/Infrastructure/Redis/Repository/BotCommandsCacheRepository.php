<?php

declare(strict_types=1);

namespace App\Module\BotCommands\Infrastructure\Redis\Repository;

use App\Module\BotCommands\DTO\DeleteBotCommandsDTO;
use App\Module\BotCommands\DTO\GetBotCommandsDTO;
use App\Module\BotCommands\DTO\SetBotCommandsDTO;
use App\Module\BotCommands\ValueObject\BotCommand;
use App\Module\BotCommands\ValueObject\BotCommandsList;
use Predis\ClientInterface;

/**
 * @see \App\Tests\Module\BotCommands\Infrastructure\Redis\Repository\BotCommandsRedisCacheRepositoryTest
 */
final readonly class BotCommandsCacheRepository
{
    private const CACHE_KEY_PREFIX = 'bot_commands_cache';
    private const CACHE_TTL_IN_SECONDS = 3600;

    public function __construct(private ClientInterface $client)
    {
    }

    public function isBotCommandsCached(GetBotCommandsDTO $getBotCommandsDTO): bool
    {
        $cacheKey = $this->getCacheKey($getBotCommandsDTO->getScope()->getType());
        return (bool)$this->client->exists($cacheKey);
    }

    public function getBotCommands(GetBotCommandsDTO $getBotCommandsDTO): BotCommandsList
    {
        $cacheKey = $this->getCacheKey($getBotCommandsDTO->getScope()->getType());
        $botCommands = $this->client->hgetall($cacheKey);
        $botCommandsList = new BotCommandsList();
        foreach ($botCommands as $command => $description) {
            $botCommandsList[] = new BotCommand($command, $description);
        }
        return $botCommandsList;
    }

    public function setBotCommands(SetBotCommandsDTO $setBotCommandsDTO, ?int $ttlInSeconds = null): void
    {
        if (null === $ttlInSeconds) {
            $ttlInSeconds = self::CACHE_TTL_IN_SECONDS;
        }
        $cacheKey = $this->getCacheKey($setBotCommandsDTO->getScope()->getType());
        $this->client->multi();
        $this->client->del($cacheKey);
        /** @var BotCommand $botCommand */
        foreach ($setBotCommandsDTO->getCommands() as $botCommand) {
            $this->client->hset($cacheKey, $botCommand->getCommand(), $botCommand->getDescription());
        }
        $this->client->expire($cacheKey, $ttlInSeconds);
        $this->client->exec();
    }

    public function deleteBotCommands(DeleteBotCommandsDTO $deleteBotCommandsDTO): void
    {
        $cacheKey = $this->getCacheKey($deleteBotCommandsDTO->getScope()->getType());
        $this->client->del($cacheKey);
    }

    private function getCacheKey(string $scopeType): string
    {
        return sprintf('%s_%s', self::CACHE_KEY_PREFIX, $scopeType);
    }
}

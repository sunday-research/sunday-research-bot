<?php

declare(strict_types=1);

namespace App\Bundle\BotCommands\Infrastructure\Redis\Repository;

use App\Bundle\BotCommands\Contract\BotCommandsCacheRepositoryInterface;
use App\Bundle\BotCommands\DTO\DeleteBotCommandsDTO;
use App\Bundle\BotCommands\DTO\GetBotCommandsDTO;
use App\Bundle\BotCommands\DTO\SetBotCommandsDTO;
use App\Bundle\BotCommands\ValueObject\BotCommand;
use App\Bundle\BotCommands\ValueObject\BotCommandsList;
use Predis\ClientInterface;

final readonly class BotCommandsRedisCacheRepository implements BotCommandsCacheRepositoryInterface
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

    /**
     * @todo: потенциально могут быть ошибки, если каким-то образом данные в кэше оказались не валидными
     */
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
        $this->client->expire($cacheKey, $ttlInSeconds, 'EX');
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

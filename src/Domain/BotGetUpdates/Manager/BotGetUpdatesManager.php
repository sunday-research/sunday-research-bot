<?php

declare(strict_types=1);

namespace App\Domain\BotGetUpdates\Manager;

use App\Bundle\BotCommands\DTO\GetBotCommandsDTO;
use App\Bundle\BotCommands\Service\BotCommandsService;
use App\Bundle\BotCommands\ValueObject\BotCommand;
use App\Bundle\BotGetUpdates\Service\BotGetUpdatesService;
use App\Domain\BotCommands\Factory\BotCommandHandlerFactory;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Exception\TelegramException;
use Psr\Log\LoggerInterface;

/**
 * @todo: сейчас менеджер заточен только на обработку команд, выглядит это как архитектурный просчёт
 */
final readonly class BotGetUpdatesManager
{
    public function __construct(
        private BotGetUpdatesService $botGetUpdatesService,
        private LoggerInterface $logger,
        private BotCommandsService $botCommandsService,
        private BotCommandHandlerFactory $botCommandHandlerFactory,
    ) {
    }

    public function run(): void
    {
        $response = $this->getUpdates();
        if (null === $response) {
            return;
        }

        /** @var Update[] $result */
        $result = $response->getResult();
        if (empty($result)) {
            return;
        }
        foreach ($result as $update) {
            if (!$this->isTypeMessage($update->getUpdateType())) {
                continue;
            }

            /** @var Message $message */
            $message = $update->getMessage();
            if ($message->getType() !== 'command') {
                continue;
            }

            foreach ($message->getEntities() as $entity) {
                if ($entity->getType() !== 'bot_command') {
                    continue;
                }

                $botCommand = $this->botCommandsService->findCommandByCommandText(
                    $message->getCommand(),
                    $update->getBotUsername(),
                    GetBotCommandsDTO::makeDTO()
                );

                if ($botCommand !== null) {
                    $this->handleBotCommand($botCommand, $update);
                }
            }
        }
    }

    private function getUpdates(): ?ServerResponse
    {
        try {
            $response = $this->botGetUpdatesService->getUpdates();
            if (!$response->isOk()) {
                $this->logger->error(
                    sprintf(
                        'Failed to getUpdates: response is not OK; error_code: `%d`, description: `%s`',
                        $response->getErrorCode(),
                        $response->getDescription(),
                    )
                );
                return null;
            }
            return $response;
        } catch (TelegramException $e) {
            $this->logger->error(
                sprintf(
                    'Failed to getUpdates: TelegramException; error: `%s`, trace: `%s`',
                    $e->getMessage(),
                    $e->getTraceAsString(),
                )
            );
            return null;
        }
    }

    private function isTypeMessage(string $updateType): bool
    {
        return in_array($updateType, [Update::TYPE_MESSAGE, Update::TYPE_EDITED_MESSAGE], true);
    }

    private function handleBotCommand(BotCommand $botCommand, Update $update): void
    {
        $botCommandHandler = $this->botCommandHandlerFactory->createBotCommandHandler($botCommand->getCommand());
        $botCommandHandler?->handle($update);
    }
}

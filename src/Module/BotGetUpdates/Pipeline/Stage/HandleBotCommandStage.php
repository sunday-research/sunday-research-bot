<?php

declare(strict_types=1);

namespace App\Module\BotGetUpdates\Pipeline\Stage;

use App\Module\BotCommands\DTO\GetBotCommandsDTO;
use App\Module\BotCommands\Factory\BotCommandHandlerFactory;
use App\Module\BotCommands\Service\BotCommandsService;
use App\Module\BotCommands\ValueObject\BotCommand;
use App\Module\BotGetUpdates\Pipeline\Payload;
use Longman\TelegramBot\Entities\Update;

final readonly class HandleBotCommandStage
{
    public function __construct(
        private BotCommandsService $botCommandsService,
        private BotCommandHandlerFactory $botCommandHandlerFactory,
    ) {
    }

    public function __invoke(Payload $payload): Payload
    {
        if ($payload->processed) {
            return $payload;
        }

        if ($payload->update->getUpdateType() !== Update::TYPE_MESSAGE) {
            return $payload;
        }

        $message = $payload->update->getMessage();
        if ($message->getType() !== 'command') {
            return $payload;
        }

        foreach ($message->getEntities() as $entity) {
            if ($entity->getType() !== 'bot_command') {
                continue;
            }

            if ($message->getCommand() === null) {
                continue;
            }

            $botCommand = $this->botCommandsService->findCommandByCommandText(
                $message->getCommand(),
                $payload->update->getBotUsername(),
                GetBotCommandsDTO::makeDTO()
            );

            if ($botCommand !== null) {
                $this->handleBotCommand($botCommand, $payload->update);
                return $payload->withProcessed(true);
            }
        }

        return $payload;
    }

    private function handleBotCommand(BotCommand $botCommand, Update $update): void
    {
        $botCommandHandler = $this->botCommandHandlerFactory->createBotCommandHandler($botCommand->getCommand());
        $botCommandHandler?->handle($update);
    }
}

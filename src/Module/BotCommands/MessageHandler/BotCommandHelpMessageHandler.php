<?php

declare(strict_types=1);

namespace App\Module\BotCommands\MessageHandler;

use App\Module\BotCommands\DTO\GetBotCommandsDTO;
use App\Module\BotCommands\DTO\SendTextMessageDTO;
use App\Module\BotCommands\Infrastructure\Telegram\SendMessageClient;
use App\Module\BotCommands\Message\BotCommandHelpMessage;
use App\Module\BotCommands\Service\BotCommandsService;
use App\Module\BotCommands\ValueObject\BotCommandsList;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class BotCommandHelpMessageHandler
{
    public function __construct(
        private SendMessageClient $telegramBotApiClient,
        private BotCommandsService $botCommandsService,
    ) {
    }

    public function __invoke(BotCommandHelpMessage $message): void
    {
        $commands = $this->botCommandsService->getBotCommands(
            GetBotCommandsDTO::makeDTO()
        );

        $helpText = $this->generateBotHelpText($commands);
        $this->telegramBotApiClient->sendTextMessage(
            SendTextMessageDTO::makeDTO(
                (string)$message->getChat()->getId(),
                $helpText
            )
        );
    }

    private function generateBotHelpText(BotCommandsList $commands): string
    {
        $lines = ["🤖 Sunday Research Bot", "", "Доступные команды:"];

        foreach ($commands as $command) {
            $lines[] = "/{$command->getCommand()} - {$command->getDescription()}";
        }

        return implode("\n", $lines);
    }
}

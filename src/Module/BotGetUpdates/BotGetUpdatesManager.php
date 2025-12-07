<?php

declare(strict_types=1);

namespace App\Module\BotGetUpdates;

/**
 * @todo: get updates зависит от bot commands; рассмотреть возможность использовать event
 */

use App\Module\BotCommands\DTO\GetBotCommandsDTO;
use App\Module\BotCommands\Enum\BotCommandsEnum;
use App\Module\BotCommands\Factory\BotCommandHandlerFactory;
use App\Module\BotCommands\Service\BotCommandsService;
use App\Module\BotCommands\ValueObject\BotCommand;
use App\Module\BotGetUpdates\Service\BotGetUpdatesService;
use App\Module\BotGetUpdates\Service\SubscriberMessageService;
use App\Module\BotGetUpdates\Service\SubscriberService;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Exception\TelegramException;
use Psr\Log\LoggerInterface;

final readonly class BotGetUpdatesManager
{
    public function __construct(
        private BotGetUpdatesService $botGetUpdatesService,
        private LoggerInterface $logger,
        private SubscriberService $subscriberService,
        private SubscriberMessageService $subscriberMessageService,
        private BotCommandsService $botCommandsService,
        private BotCommandHandlerFactory $botCommandHandlerFactory,
    ) {
    }

    public function run(): void
    {
        $response = $this->getUpdates();
        if (!$response) {
            return;
        }

        /** @var Update[] $result */
        $result = $response->getResult();
        if (empty($result)) {
            return;
        }

        foreach ($result as $update) {
            if ($update->getUpdateType() !== null && !$this->isTypeMessage($update->getUpdateType())) {
                continue;
            }

            $this->logger->debug('getUpdates result', [
                'update' => var_export($update, true),
            ]);

            /** @var Message $message */
            $message = $update->getMessage();

            // сейчас в БД сохраняются только текстовые сообщения, кроме /help
            if ($message->getType() === 'text' && !$this->isHelpCommand($message)) {
                $domainSubscriber = $this->subscriberService->create($message->getFrom());
                $this->subscriberMessageService->create($message, $domainSubscriber);
            }

            if ($message->getType() !== 'command') {
                continue;
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

    private function isHelpCommand(Message $message): bool
    {
        $text = $message->getText();

        return $text !== null && (
            $text === '/' . BotCommandsEnum::HELP->value ||
                str_starts_with($text, '/' . BotCommandsEnum::HELP->value . '@')
        );
    }
}

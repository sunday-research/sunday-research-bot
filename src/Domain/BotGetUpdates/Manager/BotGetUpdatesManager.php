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

final readonly class BotGetUpdatesManager
{
    private const UPDATE_COMMAND_STRUCTURE = <<<JSON
{
  "update": {
    "bot_username": "sunday_research_bot",
    "raw_data": {
      "update_id": 476801087,
      "message": {
        "message_id": 155,
        "from": {
          "id": 227974324,
          "is_bot": false,
          "first_name": "Владислав",
          "last_name": "Субботин",
          "username": "subbotinv",
          "language_code": "ru",
          "is_premium": true
        },
        "chat": {
          "id": -4064339494,
          "title": "SR dev",
          "type": "group",
          "all_members_are_administrators": true,
          "accepted_gift_types": {
            "unlimited_gifts": false,
            "limited_gifts": false,
            "unique_gifts": false,
            "premium_subscription": false
          }
        },
        "date": 1751623420,
        "text": "/issue@sunday_research_bot",
        "entities": [
          {
            "offset": 0,
            "length": 26,
            "type": "bot_command"
          }
        ]
      }
    },
    "fields": {
      "update_id": 476801087,
      "message": {
        "message_id": 155,
        "from": {
          "id": 227974324,
          "is_bot": false,
          "first_name": "Владислав",
          "last_name": "Субботин",
          "username": "subbotinv",
          "language_code": "ru",
          "is_premium": true
        },
        "chat": {
          "id": -4064339494,
          "title": "SR dev",
          "type": "group",
          "all_members_are_administrators": true,
          "accepted_gift_types": {
            "unlimited_gifts": false,
            "limited_gifts": false,
            "unique_gifts": false,
            "premium_subscription": false
          }
        },
        "date": 1751623420,
        "text": "/issue@sunday_research_bot",
        "entities": [
          {
            "offset": 0,
            "length": 26,
            "type": "bot_command"
          }
        ]
      }
    }
  }
}
JSON;
    private const UPDATE_TEXT_STRUCTURE = <<<JSON
{
  "update": {
    "bot_username": "sunday_research_bot",
    "raw_data": {
      "update_id": 476801086,
      "message": {
        "message_id": 154,
        "from": {
          "id": 227974324,
          "is_bot": false,
          "first_name": "Владислав",
          "last_name": "Субботин",
          "username": "subbotinv",
          "language_code": "ru",
          "is_premium": true
        },
        "chat": {
          "id": -4064339494,
          "title": "SR dev",
          "type": "group",
          "all_members_are_administrators": true,
          "accepted_gift_types": {
            "unlimited_gifts": false,
            "limited_gifts": false,
            "unique_gifts": false,
            "premium_subscription": false
          }
        },
        "date": 1751623367,
        "text": "тест"
      }
    },
    "fields": {
      "update_id": 476801086,
      "message": {
        "message_id": 154,
        "from": {
          "id": 227974324,
          "is_bot": false,
          "first_name": "Владислав",
          "last_name": "Субботин",
          "username": "subbotinv",
          "language_code": "ru",
          "is_premium": true
        },
        "chat": {
          "id": -4064339494,
          "title": "SR dev",
          "type": "group",
          "all_members_are_administrators": true,
          "accepted_gift_types": {
            "unlimited_gifts": false,
            "limited_gifts": false,
            "unique_gifts": false,
            "premium_subscription": false
          }
        },
        "date": 1751623367,
        "text": "тест"
      }
    }
  }
}
JSON;

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

            $this->logger->debug('getUpdates result', [
                'update' => var_export($update, true),
            ]);

            /** @var Message $message */
            $message = $update->getMessage();

            // сейчас в БД сохраняются только текстовые сообщения
            if ($message->getType() === 'text') {
                $domainSubscriber = $this->botGetUpdatesService->createSubscriber($message->getFrom());
                $this->botGetUpdatesService->addSubscriberMessage($message, $domainSubscriber);
            }

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

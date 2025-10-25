<?php

declare(strict_types=1);

namespace App\Module\BotGetUpdates;

/**
 * @todo: get updates зависит от bot commands; рассмотреть возможность использовать event
 */
use App\Module\BotCommands\Factory\BotCommandHandlerFactory;
use App\Module\BotCommands\Service\BotCommandsService;
use App\Module\BotGetUpdates\Pipeline\Payload;
use App\Module\BotGetUpdates\Pipeline\Stage\HandleBotCommandStage;
use App\Module\BotGetUpdates\Pipeline\Stage\UpdateTextMessageStage;
use App\Module\BotGetUpdates\Pipeline\Stage\CreateTextMessageStage;
use App\Module\BotGetUpdates\Pipeline\PipelineFactory;
use App\Module\BotGetUpdates\Service\BotGetUpdatesService;
use App\Module\BotGetUpdates\Service\SubscriberMessageService;
use App\Module\BotGetUpdates\Service\SubscriberService;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Exception\TelegramException;
use Psr\Log\LoggerInterface;

final readonly class BotGetUpdatesManager
{
    // @phpstan-ignore-next-line
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

    // @phpstan-ignore-next-line
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

    // @phpstan-ignore-next-line
    private const UPDATE_EDITED_MESSAGE_STRUCTURE = <<<JSON
{
  "update": {
    "bot_username": "sunday_research_bot",
    "raw_data": {
      "update_id": 476801088,
      "edited_message": {
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
        "edit_date": 1751623500,
        "text": "отредактированный текст"
      }
    },
    "fields": {
      "update_id": 476801088,
      "edited_message": {
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
        "edit_date": 1751623500,
        "text": "отредактированный текст"
      }
    }
  }
}
JSON;

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

            $updatePipeline = PipelineFactory::create(
                new CreateTextMessageStage($this->subscriberService, $this->subscriberMessageService),
                new UpdateTextMessageStage($this->subscriberMessageService),
                new HandleBotCommandStage($this->botCommandsService, $this->botCommandHandlerFactory),
            );

            $payload = new Payload($update);
            $updatePipeline->process($payload);
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
}

<?php

declare(strict_types=1);

namespace App\Tests\Module\BotGetUpdates\Fixtures;

use Longman\TelegramBot\Entities\Update;

final class CommandUpdateFixture extends BaseUpdateFixture
{
    public function create(): Update
    {
        $message = $this->createMessage(
            messageId: self::TEST_MESSAGE_ID_COMMAND,
            text: '/issue@sunday_research_bot',
            type: 'command',
            command: 'issue',
            entities: [$this->createBotCommandEntity()]
        );

        return $this->createUpdate(Update::TYPE_MESSAGE, $message);
    }

    public function createWithCustomCommand(string $command, string $text): Update
    {
        $message = $this->createMessage(
            messageId: self::TEST_MESSAGE_ID_COMMAND,
            text: $text,
            type: 'command',
            command: $command,
            entities: [$this->createBotCommandEntity()]
        );

        return $this->createUpdate(Update::TYPE_MESSAGE, $message);
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Module\BotGetUpdates\Fixtures;

use Longman\TelegramBot\Entities\Update;

final class CommandUpdateFixture extends BaseUpdateFixture
{
    public function create(): Update
    {
        /** @var array<string, mixed> $entityData */
        $entityData = $this->createBotCommandEntity()->getRawData();
        
        $message = $this->createMessage(
            messageId: self::TEST_MESSAGE_ID_COMMAND,
            text: '/issue@sunday_research_bot',
            type: 'command',
            command: 'issue',
            entities: [$entityData]
        );

        return $this->createUpdate(Update::TYPE_MESSAGE, $message);
    }

    public function createWithCustomCommand(string $command, string $text): Update
    {
        /** @var array<string, mixed> $entityData */
        $entityData = $this->createBotCommandEntity()->getRawData();
        
        $message = $this->createMessage(
            messageId: self::TEST_MESSAGE_ID_COMMAND,
            text: $text,
            type: 'command',
            command: $command,
            entities: [$entityData]
        );

        return $this->createUpdate(Update::TYPE_MESSAGE, $message);
    }
}

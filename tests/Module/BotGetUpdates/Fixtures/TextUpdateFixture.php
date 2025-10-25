<?php

declare(strict_types=1);

namespace App\Tests\Module\BotGetUpdates\Fixtures;

use Longman\TelegramBot\Entities\Update;

final class TextUpdateFixture extends BaseUpdateFixture
{
    public function create(): Update
    {
        $message = $this->createMessage(
            messageId: self::TEST_MESSAGE_ID_TEXT,
            text: 'тест',
            type: 'text'
        );

        return $this->createUpdate(Update::TYPE_MESSAGE, $message);
    }

    public function createWithCustomText(string $text): Update
    {
        $message = $this->createMessage(
            messageId: self::TEST_MESSAGE_ID_TEXT,
            text: $text,
            type: 'text'
        );

        return $this->createUpdate(Update::TYPE_MESSAGE, $message);
    }
}

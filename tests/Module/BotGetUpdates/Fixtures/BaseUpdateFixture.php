<?php

declare(strict_types=1);

namespace App\Tests\Module\BotGetUpdates\Fixtures;

use Longman\TelegramBot\Entities\Chat;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\MessageEntity;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Entities\User;
use PHPUnit\Framework\TestCase;

abstract class BaseUpdateFixture
{
    protected const TEST_TELEGRAM_USER_ID = 227974324;
    protected const TEST_CHAT_ID = -4064339494;
    protected const TEST_MESSAGE_ID_COMMAND = 155;
    protected const TEST_MESSAGE_ID_TEXT = 154;

    public function __construct(
        private TestCase $testCase
    ) {
    }

    protected function createUser(): User
    {
        $userData = [
            'id' => self::TEST_TELEGRAM_USER_ID,
            'is_bot' => false,
            'first_name' => 'Владислав',
            'last_name' => 'Субботин',
            'username' => 'subbotinv',
            'language_code' => 'ru',
            'is_premium' => true,
        ];
        
        return new User($userData, 'sunday_research_bot');
    }

    protected function createChat(): Chat
    {
        $chatData = [
            'id' => self::TEST_CHAT_ID,
            'title' => 'SR dev',
            'type' => 'group',
        ];
        
        return new Chat($chatData, 'sunday_research_bot');
    }

    protected function createBotCommandEntity(): MessageEntity
    {
        $entityData = [
            'type' => 'bot_command',
            'offset' => 0,
            'length' => 26,
        ];
        
        return new MessageEntity($entityData, 'sunday_research_bot');
    }

    protected function createMessage(
        int $messageId,
        string $text,
        string $type = 'text',
        ?string $command = null,
        array $entities = []
    ): Message {
        $messageData = [
            'message_id' => $messageId,
            'from' => [
                'id' => self::TEST_TELEGRAM_USER_ID,
                'is_bot' => false,
                'first_name' => 'Владислав',
                'last_name' => 'Субботин',
                'username' => 'subbotinv',
                'language_code' => 'ru',
                'is_premium' => true,
            ],
            'chat' => [
                'id' => self::TEST_CHAT_ID,
                'title' => 'SR dev',
                'type' => 'group',
            ],
            'date' => 1751623420,
            'text' => $text,
            'entities' => $entities,
        ];
        
        return new Message($messageData, 'sunday_research_bot');
    }

    protected function createUpdate(string $updateType, Message $message): Update
    {
        $updateData = [
            'update_id' => 476801087,
            'message' => $message->getRawData(),
        ];
        
        return new Update($updateData, 'sunday_research_bot');
    }

    protected function createEditedMessageUpdate(Message $message): Update
    {
        $updateData = [
            'update_id' => 476801088,
            'edited_message' => $message->getRawData(),
        ];
        
        return new Update($updateData, 'sunday_research_bot');
    }

    abstract public function create(): Update;
}

<?php

declare(strict_types=1);

namespace App\Module\BotCommands\Message;

use Longman\TelegramBot\Entities\Chat;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Entities\User;

final readonly class BotCommandHelpMessage
{
    public function __construct(private Update $update)
    {
    }

    public function getUpdate(): Update
    {
        return $this->update;
    }

    public function getMessage(): Message
    {
        return $this->getUpdate()->getMessage();
    }

    public function getFrom(): User
    {
        return $this->getMessage()->getFrom();
    }

    public function getChat(): Chat
    {
        return $this->getMessage()->getChat();
    }
}

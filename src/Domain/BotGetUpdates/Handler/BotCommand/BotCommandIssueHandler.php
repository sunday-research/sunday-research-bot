<?php

declare(strict_types=1);

namespace App\Domain\BotGetUpdates\Handler\BotCommand;

use App\Domain\BotGetUpdates\Contract\BotCommandHandlerInterface;
use Longman\TelegramBot\Entities\Update;

final class BotCommandIssueHandler implements BotCommandHandlerInterface
{
    public function handle(Update $update): void
    {
        // Возможно, здесь должен быть просто вызов брокера сообщений, поскольку эту команду можно обработать в фоне
        // Вернуть ответ пользователю, что его запрос успешно принят и будет передан владельцу
        // Если возможно, сразу же создать какую-то ссылку для отслеживания запроса
        $message = $update->getMessage();
        $chat = $message->getChat();
        $from = $message->getFrom();
    }
}

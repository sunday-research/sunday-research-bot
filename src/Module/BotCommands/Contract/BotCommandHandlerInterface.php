<?php

declare(strict_types=1);

namespace App\Module\BotCommands\Contract;

use Longman\TelegramBot\Entities\Update;

/**
 * @todo: после перехода на pipeline надобность в этом интерфейсе отпадёт
 */
interface BotCommandHandlerInterface
{
    public function handle(Update $update): void;
}

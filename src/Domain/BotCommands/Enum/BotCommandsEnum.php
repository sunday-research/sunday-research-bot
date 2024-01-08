<?php

declare(strict_types=1);

namespace App\Domain\BotCommands\Enum;

enum BotCommandsEnum: string
{
    case ISSUE = 'issue';

    public static function getAllCommands(): array
    {
        return [
            [
                'command' => self::ISSUE->value,
                'description' => 'Отправить предложение об улучшении бота или о добавлении нового функционала',
            ],
        ];
    }
}

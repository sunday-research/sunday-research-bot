<?php

declare(strict_types=1);

namespace App\Module\BotCommands\Enum;

enum BotCommandsEnum: string
{
    case ISSUE = 'issue';

    /**
     * @return array<int, array<string, string>>
     */
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

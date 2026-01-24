<?php

declare(strict_types=1);

namespace App\Module\BotCommands\Enum;

enum IssuePriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';

    public static function default(): self
    {
        return self::MEDIUM;
    }
}

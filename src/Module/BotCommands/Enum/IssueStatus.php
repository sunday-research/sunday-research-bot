<?php

declare(strict_types=1);

namespace App\Module\BotCommands\Enum;

enum IssueStatus: string
{
    case NEW = 'new';
    case IN_PROGRESS = 'in_progress';
    case RESOLVED = 'resolved';

    public static function default(): self
    {
        return self::NEW;
    }
}

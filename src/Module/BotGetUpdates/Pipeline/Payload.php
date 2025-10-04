<?php

declare(strict_types=1);

namespace App\Module\BotGetUpdates\Pipeline;

use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\Update;

final readonly class Payload
{
    public function __construct(
        public Update $update,
        public ?Message $message = null,
        public bool $processed = false,
    ) {
    }

    public function withProcessed(bool $processed): self
    {
        return new self(
            update: $this->update,
            message: $this->message,
            processed: $processed,
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Module\BotCommands\ValueObject;

final readonly class BotCommand
{
    public function __construct(
        private string $command,
        private string $description
    ) {
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'command' => $this->getCommand(),
            'description' => $this->getDescription(),
        ];
    }
}

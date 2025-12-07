<?php

declare(strict_types=1);

namespace App\Module\BotCommands\ValueObject;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * @implements ArrayAccess<int, BotCommand>
 * @implements IteratorAggregate<int, BotCommand>
 */
class BotCommandsList implements Countable, IteratorAggregate, ArrayAccess
{
    /**
     * @var BotCommand[]
     */
    private array $botCommands = [];

    public function count(): int
    {
        return count($this->botCommands);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->botCommands);
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->botCommands);
    }

    /**
     * @param mixed $offset
     * @return BotCommand|null
     */
    public function offsetGet(mixed $offset): ?BotCommand
    {
        return $this->botCommands[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (null === $offset) {
            $this->botCommands[] = $value;
        } else {
            $this->botCommands[$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->botCommands[$offset]);
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function toArray(): array
    {
        $result = [];
        /** @var BotCommand $botCommand */
        foreach ($this->botCommands as $botCommand) {
            $result[] = $botCommand->toArray();
        }

        return $result;
    }
}

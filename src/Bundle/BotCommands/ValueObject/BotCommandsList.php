<?php

declare(strict_types=1);

namespace App\Bundle\BotCommands\ValueObject;

use App\Bundle\BotCommands\Exception\BotCommandsListAddCommandException;
use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

final class BotCommandsList implements Countable, IteratorAggregate, ArrayAccess
{
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

    public function offsetGet(mixed $offset): mixed
    {
        return $this->botCommands[$offset];
    }

    /**
     * @throws BotCommandsListAddCommandException
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!$value instanceof BotCommand) {
            throw new BotCommandsListAddCommandException(
                'Incorrect instance of BotCommand. Provided ' . get_class($value)
            );
        }

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

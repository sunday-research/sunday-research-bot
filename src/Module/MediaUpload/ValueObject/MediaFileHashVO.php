<?php

declare(strict_types=1);

namespace App\Module\MediaUpload\ValueObject;

final readonly class MediaFileHashVO
{
    private function __construct(private string $hash)
    {
    }

    public static function fromFilePath(string $filePath): self
    {
        return new self(hash('sha256', $filePath));
    }

    public static function fromString(string $hash): self
    {
        return new self($hash);
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function equals(self $other): bool
    {
        return $this->hash === $other->hash;
    }

    public function __toString(): string
    {
        return $this->hash;
    }
}

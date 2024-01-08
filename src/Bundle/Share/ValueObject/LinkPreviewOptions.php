<?php

declare(strict_types=1);

namespace App\Bundle\Share\ValueObject;

readonly class LinkPreviewOptions
{
    public function __construct(
        private bool $isDisabled = false,
        private ?string $url = null,
        private bool $preferSmallMedia = false,
        private bool $preferLargeMedia = false,
        private bool $showAboveText = false,
    ) {
    }

    public function isDisabled(): bool
    {
        return $this->isDisabled;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function isPreferSmallMedia(): bool
    {
        return $this->preferSmallMedia;
    }

    public function isPreferLargeMedia(): bool
    {
        return $this->preferLargeMedia;
    }

    public function isShowAboveText(): bool
    {
        return $this->showAboveText;
    }

    public function toArray(): array
    {
        return [
            'isDisabled' => $this->isDisabled(),
            'url' => $this->getUrl(),
            'preferSmallMedia' => $this->isPreferSmallMedia(),
            'preferLargeMedia' => $this->isPreferLargeMedia(),
            'showAboveText' => $this->isShowAboveText(),
        ];
    }
}

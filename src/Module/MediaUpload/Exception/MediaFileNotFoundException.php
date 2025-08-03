<?php

declare(strict_types=1);

namespace App\Module\MediaUpload\Exception;

class MediaFileNotFoundException extends MediaUploadException
{
    public function __construct(string $filePath)
    {
        parent::__construct(sprintf('Media file not found: %s', $filePath));
    }
} 
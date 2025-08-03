<?php

declare(strict_types=1);

namespace App\Module\MediaUpload\Enum;

enum MediaTypeEnum: string
{
    case PHOTO = 'photo';
    case ANIMATION = 'animation';
    case VIDEO = 'video';
    case AUDIO = 'audio';
    case DOCUMENT = 'document';
    case VOICE = 'voice';
    case VIDEO_NOTE = 'video_note';
    case STICKER = 'sticker';

    public function getTelegramMethod(): string
    {
        return match ($this) {
            self::PHOTO => 'sendPhoto',
            self::ANIMATION => 'sendAnimation',
            self::VIDEO => 'sendVideo',
            self::AUDIO => 'sendAudio',
            self::DOCUMENT => 'sendDocument',
            self::VOICE => 'sendVoice',
            self::VIDEO_NOTE => 'sendVideoNote',
            self::STICKER => 'sendSticker',
        };
    }

    public function getFileField(): string
    {
        return match ($this) {
            self::PHOTO => 'photo',
            self::ANIMATION => 'animation',
            self::VIDEO => 'video',
            self::AUDIO => 'audio',
            self::DOCUMENT => 'document',
            self::VOICE => 'voice',
            self::VIDEO_NOTE => 'video_note',
            self::STICKER => 'sticker',
        };
    }
}

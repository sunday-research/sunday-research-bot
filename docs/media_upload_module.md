# Модуль MediaUpload

Модуль для загрузки медиа-файлов в Telegram с кэшированием в Redis.

## Описание

Модуль `MediaUpload` предназначен для эффективной работы с медиа-файлами в Telegram боте. Он автоматически загружает файлы в Telegram и кэширует полученные `file_id` в Redis, что позволяет избежать повторной загрузки одних и тех же файлов.

## Архитектура

### Основные компоненты

- **MediaUploadService** - основной сервис для работы с медиа-файлами
- **MediaUploadClient** - клиент для взаимодействия с Telegram API
- **MediaUploadCacheRepository** - репозиторий для работы с кэшем Redis
- **MediaFileHash** - ValueObject для хэширования путей к файлам

### Структура директорий

```
src/Module/MediaUpload/
├── Contract/          # Интерфейсы
├── DTO/               # Data Transfer Objects
├── Enum/              # Перечисления
├── Exception/         # Исключения
├── Factory/           # Фабрики
├── Handler/           # Обработчики
├── Infrastructure/    # Инфраструктурный слой
│   ├── Redis/         # Работа с Redis
│   └── Telegram/      # Работа с Telegram API
├── Message/           # Сообщения
├── MessageHandler/    # Обработчики сообщений
├── Service/           # Бизнес-логика
└── ValueObject/       # Value Objects
```

## Использование

### Базовое использование

```php
use App\Module\MediaUpload\Service\MediaUploadService;
use App\Module\MediaUpload\DTO\UploadMediaDTO;
use App\Module\MediaUpload\Enum\MediaTypeEnum;

// Получение file_id для файла
$fileId = $mediaUploadService->getMediaFileId(
    'assets/media/animations/friday.gif',
    'animation'
);

// Загрузка файла с полной информацией
$uploadDTO = UploadMediaDTO::makeDTO(
    filePath: 'assets/media/images/logo.png',
    mediaType: MediaTypeEnum::PHOTO,
    caption: 'Логотип компании',
    parseMode: 'MarkdownV2'
);

$fileInfo = $mediaUploadService->uploadMediaWithCache($uploadDTO);
```

### Интеграция с SendMediaMessageDTO

Модуль интегрирован с `SendMediaMessageDTO` для автоматического определения типа медиа:

```php
// Для локальных файлов (автоматически получает file_id)
$dto = SendMediaMessageDTO::makeDTO(
    chatId: $chatId,
    media: 'assets/media/animations/friday.gif',
    mediaType: 'animation'
    // isFileId определяется автоматически
);

// Для внешних ссылок
$dto = SendMediaMessageDTO::makeDTO(
    chatId: $chatId,
    media: 'https://example.com/image.jpg',
    mediaType: 'photo'
    // isFileId определяется автоматически
);

// Для уже полученных file_id
$dto = SendMediaMessageDTO::makeDTO(
    chatId: $chatId,
    media: $fileId,
    mediaType: 'animation'
    // isFileId определяется автоматически
);
```

## Кэширование

### Алгоритм хэширования

Для избежания коллизий используется SHA-256 хэш от пути к файлу:

```php
$hash = hash('sha256', $filePath);
```

### Redis структура

Кэш хранится в Redis с использованием хэш-таблиц:

```
Key: media_upload_cache_{hash}
Fields:
- file_id: string
- file_unique_id: string  
- media_type: string
- file_size: int (optional)
- file_path: string (optional)
TTL: 86400 секунд (24 часа)
```

### Операции с кэшем

- **NX (Only if Not eXists)** - новые записи добавляются только если ключ не существует
- **TTL** - автоматическое удаление через 24 часа
- **Atomic operations** - использование транзакций для атомарности

## Поддерживаемые типы медиа

- `photo` - изображения (JPG, PNG, WEBP)
- `animation` - GIF анимации
- `video` - видео файлы (MP4, AVI, MOV)
- `audio` - аудио файлы (MP3, OGG, WAV)
- `document` - документы (PDF, DOC, TXT)
- `voice` - голосовые сообщения
- `video_note` - видеосообщения
- `sticker` - стикеры

## Тестирование

### Тестовая команда

```bash
# Тест загрузки медиа-файлов
php bin/console test:media-upload

# Тест отправки Friday медиа
php bin/console test:send-friday-media
```

### Примеры тестов

1. **Загрузка локального файла** - файл загружается в Telegram и кэшируется
2. **Повторная загрузка** - используется кэшированный file_id
3. **Внешние ссылки** - отправляются напрямую без загрузки
4. **Очистка кэша** - удаление записей из Redis

## Конфигурация

### TTL кэша

По умолчанию TTL установлен на 24 часа (86400 секунд). Можно изменить в `MediaUploadCacheRepository`:

```php
private const CACHE_TTL_IN_SECONDS = 86400; // 24 hours
```

### Префикс ключей Redis

```php
private const CACHE_KEY_PREFIX = 'media_upload_cache';
```

## Обработка ошибок

### Исключения

- `MediaUploadFailedException` - ошибка загрузки в Telegram
- `MediaFileNotFoundException` - файл не найден
- `MediaUploadException` - базовый класс исключений

### Логирование

Все операции логируются через стандартную систему логирования Symfony.

## Производительность

### Оптимизации

1. **Кэширование** - избежание повторных загрузок
2. **NX операции** - предотвращение коллизий
3. **TTL** - автоматическая очистка старых записей
4. **Атомарные операции** - использование Redis транзакций

### Мониторинг

- Время загрузки файлов
- Размер кэша Redis
- Количество кэш-попаданий/промахов
- Ошибки загрузки

## Безопасность

### Валидация файлов

- Проверка существования файла
- Валидация типов файлов
- Ограничение размера файлов

### Redis безопасность

- Использование префиксов для изоляции
- TTL для предотвращения утечек памяти
- Валидация данных перед сохранением 
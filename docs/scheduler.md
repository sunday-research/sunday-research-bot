# Symfony Scheduler

Документация по настройке и использованию Symfony Scheduler в проекте Sunday Research Bot.

## Обзор

Symfony Scheduler используется для автоматической отправки медиа-сообщений каждую пятницу в 8:00. Архитектура состоит из:

- **FridayMediaScheduleProvider** - провайдер расписания
- **SendFridayMediaMessage** - сообщение-триггер
- **SendFridayMediaHandler** - обработчик сообщения

## Архитектура

```
Scheduler (планировщик)
    ↓
FridayMediaScheduleProvider (провайдер расписаний)
    ↓
RecurringMessage (повторяющиеся сообщения)
    ↓
Messenger (очередь сообщений)
    ↓
SendFridayMediaHandler (обработчики)
```

## Компоненты

### 1. FridayMediaScheduleProvider
- **Файл**: `src/Scheduler/FridayMediaScheduleProvider.php`
- **Назначение**: Определяет расписание (каждую пятницу в 8:00)
- **Cron выражение**: `0 8 * * 5`

### 2. SendFridayMediaMessage
- **Файл**: `src/Scheduler/Message/SendFridayMediaMessage.php`
- **Назначение**: Сообщение-триггер с данными для отправки
- **Содержит**: chat_id, media URL, caption, mediaType

### 3. SendFridayMediaHandler
- **Файл**: `src/Scheduler/Handler/SendFridayMediaHandler.php`
- **Назначение**: Обрабатывает сообщение и отправляет медиа
- **Использует**: SendMessageClient для отправки в Telegram

## Конфигурация

### Переменные окружения
```bash
# .env.local
SUNDAY_RESEARCH_CHAT_ID=-1001326836344  # Chat ID с префиксом 100
```

### Параметры в services.yaml
```yaml
parameters:
    app.telegram.sunday_research_chat_id: '%env(string:SUNDAY_RESEARCH_CHAT_ID)%'
```

## Запуск Scheduler

### 1. Ручной запуск
```bash
# Запуск на 5 минут
php bin/console messenger:consume scheduler_friday_media --time-limit=300

# Непрерывная работа (до Ctrl+C)
php bin/console messenger:consume scheduler_friday_media
```

### 2. Автоматический запуск через Cron
```bash
# Добавить в crontab (crontab -e)
# Каждую минуту проверяем расписания
* * * * * cd /path/to/your/project && php bin/console messenger:consume scheduler_friday_media --time-limit=60
```

### 3. Через Supervisor (рекомендуется для продакшена)
Создать файл `/etc/supervisor/conf.d/symfony-scheduler.conf`:
```ini
[program:symfony-scheduler]
command=php /path/to/project/bin/console messenger:consume scheduler_friday_media
directory=/path/to/project
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/symfony-scheduler.log
```

Затем:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start symfony-scheduler
```

## Отладка и мониторинг

### Просмотр расписаний
```bash
php bin/console debug:scheduler
```

### Просмотр зарегистрированных провайдеров
```bash
php bin/console debug:container --tag=scheduler.schedule_provider
```

### Тестирование отправки
```bash
# Тестовая команда для немедленной отправки
php bin/console test:send-friday-media
```

## Расписание

- **Частота**: Каждую пятницу
- **Время**: 08:00 UTC
- **Cron выражение**: `0 8 * * 5`
- **Следующий запуск**: Отображается в `debug:scheduler`

## Транспорт Messenger

Scheduler создает автоматически транспорт:
- **Название**: `scheduler_friday_media`
- **Тип**: `schedule://friday_media`
- **Автоматическая регистрация**: Да

## Устранение неполадок

### Ошибка "chat not found"
1. Проверить, что бот добавлен в чат
2. Проверить права бота (должен быть администратор)
3. Убедиться, что chat_id содержит префикс `-100` для закрытых групповых чатов

Подробную информацию смотри по ссылкам:
https://stackoverflow.com/a/32572159
https://aliyorov.com/all/prostoy-sposob-uznat-chat-aydi-zakrytoy-gruppy-v-telegrame/

### Провайдер не зарегистрирован
1. Проверить тег в `services.yaml`
2. Очистить кэш: `php bin/console cache:clear`

### Команда не найдена
В Symfony Scheduler 7.0.x используется `messenger:consume` вместо `scheduler:consume`

## Преимущества Symfony Scheduler

- ✅ **Централизованное управление** - все расписания в коде
- ✅ **Интеграция с Messenger** - асинхронное выполнение
- ✅ **Гибкость** - легко изменить расписания
- ✅ **Мониторинг** - встроенная поддержка логирования
- ✅ **Тестируемость** - можно тестировать отдельно
- ✅ **Современный подход** - рекомендован в Symfony 7.x

## Отличие от традиционных cron-задач

| Cron | Symfony Scheduler |
|------|------------------|
| Отдельные процессы | Единая система |
| Файлы конфигурации | Код в PHP |
| Сложно тестировать | Легко тестировать |
| Нет мониторинга | Встроенный мониторинг | 
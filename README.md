# Sunday Research Bot

![](https://i.imgur.com/ywXyTAV.jpg)

## Описание
Телеграм-бот, написанный специально для [чата Sunday Research](https://t.me/sunday_research),
который изначально задумывался всего с одной целью: отправлять в чат .gif каждую пятницу (местная традиция).

Однако это слишком небольшая задача, которая может быть решена в пару строк кода. Поэтому всё же бот был задуман
с более глобальной целью: предоставить пользователям чата небольшие функции для более комфортного общения.
Например, такие как сохранение заметок и их последующий поиск, карма (возможность оценивать сообщения участников),
создание стикеров, использование ChatGPT и прочих вещей, которые мы только захотим внедрить для нашего удобства.

Все большие вещи начинаются с малого. Лучше сделать один маленький шаг (или 2-3 маленьких шага), чем приготовиться
к большому пути и так и не начать шагать. Поэтому этот бот сперва будет решать всего одну задачу:
отправлять .gif в чат каждую пятницу. А по ходу дела &ndash; посмотрим. Если у вас есть идеи, что ещё мог бы делать бот,
я буду рад их увидеть в issue в этом репозитории или в самом чате. Кстати, вполне возможно, что я добавлю сразу же
команду, которая позволит боту принимать предложения по улучшению.

## Как развернуть проект
1. Скачать репозиторий на локальную машину: `git@github.com:sunday-research/sunday-research-bot.git`
2. Скопировать файл с переменными окружения: `cp .env.dist .env`
3. Заполнить переменные `SUNDAY_RESEARCH_TELEGRAM_BOT_API_KEY`, `SUNDAY_RESEARCH_TELEGRAM_BOT_USERNAME` данными своего тестового Телеграм бота (создать тестового бота можно через [BotFather](https://t.me/BotFather))
4. Запустить контейнеры командой `docker compose -f compose.yaml up -d`
5. Установить зависимости Composer: `docker compose -f compose.yaml exec symfony composer install`

## Технические детали
Сейчас обновления для бота поступают в систему путём опроса (polling) Telegram Bot API, а не веб-хуков.
Запускается опрос консольной командой `docker compose -f compose.yaml exec symfony ./bin/console app:bot-update-listener`.
Этот метод удобен для разработки функционала, когда домена ещё нет.

Сам код состоит из следующих структур:
1. `Domain` &ndash; содержит код, который отвечает за бизнес-логику
2. `Controller` &ndash; эта директория для HTTP API, тут пока ничего нет, но могли бы быть веб-хуки
3. `Command` &ndash; эта директория содержит CLI команды
4. `Bundle` &ndash; эта важная часть, так называемые модули, которые могут быть использованы и переиспользованы в Domain

Вот, что ещё нужно знать про структуру. Есть иерархия, которая выглядит следующим образом:
1. `<Class>Manager` (в директории Domain) &ndash; это самый верхнеуровневый логический класс, который отвечает за бизнес-логику. Из него вызываются сервисы, а из сервисов &ndash; репозитории
2. `<Class>Service` &ndash; это такой класс-кирпичик, которых в Manager'е может быть использовано несколько. Например, сервис для работы с подписками. Или сервис для работы с пользователями. Сервис, в свою очередь, может работать с репозиториями
3. `<Class>Repository` &ndash; это класс, который работает с каким-либо хранилищем или ресурсом (API). Например, это может быть БД, Redis или Telegram Bot API. Всё это является по сути репозиторием

Менеджер может быть вызван из любой точки входа, например, CLI команды или Controller'а. Желательно использовать именно менеджер, поскольку именно он отвечает за бизнес-логику, и не вызывать напрямую сервис или репозиторий.
Хотя иногда это допустимо для отдельных задач, для которых описывать сервис или какой-то метод в менеджере избыточно и бессмысленно. В остальных случаях стек вызова такой: CLI -> Manager -> Service -> Repository или Controller -> Manager -> Service -> Repository.
Для репозиториев обязательно использовать интерфейсы

### Планы на будущее относительно архитектуры
Что нужно проработать в ближайшее время с точки зрения архитектуры:
1. Поскольку сценарии для команд бота могут состоять более чем из одного шага и могут быть сложными и разнообразными, а взаимодействовать с ботом в рамках одного чата могут сразу несколько пользователей, необходим механизм, который будет на основании структуры сценария команды и текущей сессии с конкретным пользователем определять дальнейший шаг.
2. Необходимо также спроектировать планировщик событий, на основании которых бот будет отправлять сообщения в чат.
3. Проработать формат сообщений. Пока что ориентируемся только на текстовые сообщения. В дальнейшем, когда будет готова архитектурная основа, можно будет наращивать функционал.

## Лицензия
MIT

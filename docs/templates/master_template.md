# Главный шаблон разработки Sunday Research Bot

## Обзор

Этот документ является основным шаблоном для разработки в проекте Sunday Research Bot. Он объединяет все стандарты, принципы и лучшие практики, которые должны соблюдаться при написании кода.

## Технический стек

### Основные технологии
- **PHP**: 8.2.9+ с использованием современных возможностей
- **Symfony**: 7.0+ с соблюдением лучших практик фреймворка
- **Doctrine ORM**: 3.2+ для работы с базой данных
- **PostgreSQL**: Основная база данных
- **Redis**: Кэширование и очереди сообщений

### Инструменты качества кода
- **PHPStan**: Уровень 10 (максимальный) для статического анализа
- **PHP-CS-Fixer**: PSR-12 стандарт для форматирования кода
- **PHPUnit**: 10.5+ для тестирования
- **Composer**: Управление зависимостями

## Архитектурные принципы

### 1. Domain-Driven Design (DDD)
- Каждый модуль представляет отдельный bounded context
- Использование ubiquitous language в коде
- Четкое разделение на слои архитектуры

### 2. Clean Architecture
- Зависимости направлены внутрь (Dependency Rule)
- Внутренние слои не зависят от внешних
- Использование интерфейсов для абстракции

### 3. SOLID принципы
- **S**ingle Responsibility Principle
- **O**pen/Closed Principle  
- **L**iskov Substitution Principle
- **I**nterface Segregation Principle
- **D**ependency Inversion Principle

### 4. Модульная архитектура
- Low coupling между модулями
- High cohesion внутри модулей
- Четкие границы ответственности

## Структура проекта

### Модульная организация
```
src/
├── Module/
│   ├── {ModuleName}/
│   │   ├── Contract/           # Интерфейсы и контракты
│   │   ├── DTO/               # Data Transfer Objects
│   │   ├── Entity/            # Доменные сущности
│   │   ├── Enum/              # Перечисления
│   │   ├── Exception/         # Исключения модуля
│   │   ├── Factory/           # Фабрики
│   │   ├── Handler/           # Обработчики
│   │   ├── Infrastructure/    # Инфраструктурный слой
│   │   │   ├── Doctrine/      # Работа с БД
│   │   │   ├── Redis/         # Работа с Redis
│   │   │   └── Telegram/      # Внешние API
│   │   ├── Message/           # Сообщения для Messenger
│   │   ├── MessageHandler/    # Обработчики сообщений
│   │   ├── Service/           # Сервисы бизнес-логики
│   │   └── ValueObject/       # Объекты-значения
│   └── ...
├── Command/                   # CLI команды
├── Controller/                # HTTP контроллеры
└── Share/                     # Общие компоненты
```

### Иерархия вызовов
```
CLI/Controller → Manager → Service → Repository
```

## Стандарты кодирования

### 1. Общие требования

#### Обязательные элементы
```php
<?php

declare(strict_types=1);

namespace App\Module\{ModuleName}\{Layer};

use App\Module\{ModuleName}\{Dependency};
use {ExternalDependency};

/**
 * Описание назначения класса
 * 
 * @todo: описание TODO если есть
 */
final readonly class {ClassName}
{
    public function __construct(
        private {DependencyType} ${dependencyName},
    ) {
    }
}
```

#### Типизация
- Все методы должны иметь типы параметров и возвращаемых значений
- Использовать `readonly` классы где это уместно
- Добавлять `@phpstan-ignore-next-line` только при необходимости
- Использовать строгую типизацию везде, где это возможно

### 2. Именование

#### Классы
- **Manager**: Высокоуровневые классы бизнес-логики
- **Service**: Классы-кирпичики для конкретных операций
- **Repository**: Классы для работы с хранилищами
- **Factory**: Классы для создания объектов
- **Handler**: Классы для обработки событий/команд
- **DTO**: Объекты для передачи данных
- **ValueObject**: Неизменяемые объекты-значения

#### Методы
- Использовать глаголы в инфинитиве: `create()`, `update()`, `delete()`
- Для получения данных: `get*()`, `find*()`, `retrieve*()`
- Для проверок: `is*()`, `has*()`, `can*()`

### 3. Документация

#### PHPDoc
```php
/**
 * Описание метода
 * 
 * @param {ParamType} ${paramName} описание параметра
 * @return {ReturnType} описание возвращаемого значения
 * @throws {ExceptionType} когда выбрасывается исключение
 */
public function {methodName}({ParamType} ${paramName}): {ReturnType}
{
    // implementation
}
```

## Шаблоны классов

### 1. Manager класс
```php
<?php

declare(strict_types=1);

namespace App\Module\{ModuleName};

use App\Module\{ModuleName}\Service\{ServiceName};
use Psr\Log\LoggerInterface;

/**
 * Высокоуровневый менеджер для управления {DomainConcept}
 */
final readonly class {ModuleName}Manager
{
    public function __construct(
        private {ServiceName} ${serviceName},
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Основной метод для выполнения бизнес-операции
     */
    public function execute{Operation}(): void
    {
        try {
            $this->logger->info('Starting {operation}', [
                'context' => 'additional info'
            ]);

            $result = $this->{serviceName}->perform{Operation}();

            $this->logger->info('{Operation} completed successfully', [
                'result' => $result
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to execute {operation}', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
```

### 2. Service класс
```php
<?php

declare(strict_types=1);

namespace App\Module\{ModuleName}\Service;

use App\Module\{ModuleName}\Infrastructure\{InfrastructureLayer}\Repository\{RepositoryName};
use App\Module\{ModuleName}\DTO\{DTOName};
use App\Module\{ModuleName}\ValueObject\{ValueObjectName};

/**
 * Сервис для работы с {DomainConcept}
 */
final readonly class {ServiceName}
{
    public function __construct(
        private {RepositoryName} ${repositoryName},
    ) {
    }

    /**
     * Создает новый {DomainConcept}
     * 
     * @param {DTOName} $dto данные для создания
     * @return {ValueObjectName} созданный объект
     * @throws {ExceptionName} если создание не удалось
     */
    public function create{Entity}({DTOName} $dto): {ValueObjectName}
    {
        $this->validate{Entity}Data($dto);
        $entity = {EntityName}::fromDTO($dto);
        $this->{repositoryName}->save($entity);
        return $entity;
    }

    /**
     * Валидирует данные для создания {DomainConcept}
     * 
     * @param {DTOName} $dto данные для валидации
     * @throws {ValidationException} если данные невалидны
     */
    private function validate{Entity}Data({DTOName} $dto): void
    {
        // Реализация валидации
    }
}
```

### 3. Repository класс
```php
<?php

declare(strict_types=1);

namespace App\Module\{ModuleName}\Infrastructure\{InfrastructureLayer}\Repository;

use App\Module\{ModuleName}\Infrastructure\{InfrastructureLayer}\Model\{ModelName};
use App\Module\{ModuleName}\ValueObject\{ValueObjectName};
use Doctrine\ORM\EntityManagerInterface;

/**
 * Репозиторий для работы с {DomainConcept} в {InfrastructureLayer}
 */
final readonly class {RepositoryName}
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Сохраняет {DomainConcept}
     * 
     * @param {ValueObjectName} ${entityName} объект для сохранения
     */
    public function save({ValueObjectName} ${entityName}): void
    {
        $model = {ModelName}::fromDomain($entityName);
        $this->entityManager->persist($model);
        $this->entityManager->flush();
    }

    /**
     * Находит {DomainConcept} по ID
     * 
     * @param string $id идентификатор
     * @return {ValueObjectName}|null найденный объект или null
     */
    public function findById(string $id): ?{ValueObjectName}
    {
        $model = $this->entityManager->find({ModelName}::class, $id);
        return $model ? {ValueObjectName}::fromModel($model) : null;
    }
}
```

### 4. DTO класс
```php
<?php

declare(strict_types=1);

namespace App\Module\{ModuleName}\DTO;

/**
 * DTO для передачи данных {DomainConcept}
 */
final readonly class {DTOName}
{
    public function __construct(
        private string $field1,
        private int $field2,
        private ?string $optionalField = null,
    ) {
    }

    public function getField1(): string
    {
        return $this->field1;
    }

    public function getField2(): int
    {
        return $this->field2;
    }

    public function getOptionalField(): ?string
    {
        return $this->optionalField;
    }

    /**
     * Создает DTO из массива данных
     * 
     * @param array<string, mixed> $data исходные данные
     * @return self созданный DTO
     */
    public static function fromArray(array $data): self
    {
        return new self(
            field1: (string) $data['field1'],
            field2: (int) $data['field2'],
            optionalField: isset($data['optional_field']) ? (string) $data['optional_field'] : null,
        );
    }

    /**
     * Преобразует DTO в массив
     * 
     * @return array<string, mixed> массив данных
     */
    public function toArray(): array
    {
        return [
            'field1' => $this->field1,
            'field2' => $this->field2,
            'optional_field' => $this->optionalField,
        ];
    }
}
```

### 5. Value Object класс
```php
<?php

declare(strict_types=1);

namespace App\Module\{ModuleName}\ValueObject;

/**
 * Объект-значение для {DomainConcept}
 */
final readonly class {ValueObjectName}
{
    public function __construct(
        private string $value,
    ) {
        $this->validate($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Проверяет равенство с другим объектом
     * 
     * @param {ValueObjectName} $other объект для сравнения
     * @return bool результат сравнения
     */
    public function equals({ValueObjectName} $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Валидирует значение
     * 
     * @param string $value значение для валидации
     * @throws {ValidationException} если значение невалидно
     */
    private function validate(string $value): void
    {
        if (empty($value)) {
            throw new {ValidationException}('Value cannot be empty');
        }
    }

    /**
     * Создает объект из строки
     * 
     * @param string $value строковое значение
     * @return self созданный объект
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
```

## Шаблоны тестов

### 1. Unit тест
```php
<?php

declare(strict_types=1);

namespace App\Tests\Module\{ModuleName}\{Layer};

use App\Module\{ModuleName}\{Layer}\{ClassName};
use PHPUnit\Framework\TestCase;

/**
 * Тесты для {ClassName}
 */
final class {ClassName}Test extends TestCase
{
    private {ClassName} ${className};

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->{className} = new {ClassName}(
            // зависимости...
        );
    }

    /**
     * @test
     */
    public function it_should_{expected_behavior}(): void
    {
        // Arrange
        $input = 'test input';

        // Act
        $result = $this->{className}->{methodName}($input);

        // Assert
        $this->assertSame('expected result', $result);
    }

    /**
     * @test
     */
    public function it_should_throw_exception_when_{condition}(): void
    {
        // Arrange
        $invalidInput = 'invalid input';

        // Assert
        $this->expectException({ExceptionClass}::class);
        $this->expectExceptionMessage('Expected error message');

        // Act
        $this->{className}->{methodName}($invalidInput);
    }
}
```

### 2. Integration тест
```php
<?php

declare(strict_types=1);

namespace App\Tests\Module\{ModuleName}\Integration;

use App\Module\{ModuleName}\{ModuleName}Manager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Integration тесты для {ModuleName}Manager
 */
final class {ModuleName}ManagerIntegrationTest extends KernelTestCase
{
    private {ModuleName}Manager ${moduleName}Manager;

    protected function setUp(): void
    {
        parent::setUp();
        
        $container = static::getContainer();
        $this->{moduleName}Manager = $container->get({ModuleName}Manager::class);
    }

    /**
     * @test
     */
    public function it_should_execute_{operation}_end_to_end(): void
    {
        // Arrange
        $inputData = 'test input data';

        // Act
        $this->{moduleName}Manager->execute{Operation}($inputData);

        // Assert
        // Проверки результата
    }
}
```

## Обработка ошибок

### 1. Иерархия исключений
```php
<?php

declare(strict_types=1);

namespace App\Module\{ModuleName}\Exception;

use Exception;

/**
 * Базовое исключение для {ModuleName} модуля
 */
abstract class {ModuleName}Exception extends Exception
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Exception $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}

/**
 * Исключение для {ModuleName} модуля
 */
final class {ExceptionName} extends {ModuleName}Exception
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Exception $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Создает исключение с контекстом
     * 
     * @param string $operation операция, которая вызвала ошибку
     * @param array<string, mixed> $context контекст ошибки
     * @return self созданное исключение
     */
    public static function withContext(string $operation, array $context): self
    {
        $message = sprintf(
            'Failed to %s. Context: %s',
            $operation,
            json_encode($context, JSON_UNESCAPED_UNICODE)
        );

        return new self($message);
    }
}
```

### 2. Логирование
```php
// Использование PSR-3 Logger
$this->logger->info('Operation started', [
    'operation' => 'user_registration',
    'user_id' => $user->getId()->toString()
]);

$this->logger->error('Operation failed', [
    'operation' => 'user_registration',
    'error' => $e->getMessage(),
    'trace' => $e->getTraceAsString()
]);
```

## Конфигурация и зависимости

### 1. Dependency Injection
```php
// services.yaml
services:
    App\Module\{ModuleName}\{ModuleName}Manager:
        arguments:
            $service: '@App\Module\{ModuleName}\Service\{ServiceName}'
            $logger: '@logger'

    App\Module\{ModuleName}\Service\{ServiceName}:
        arguments:
            $repository: '@App\Module\{ModuleName}\Infrastructure\{InfrastructureLayer}\Repository\{RepositoryName}'

    App\Module\{ModuleName}\Infrastructure\{InfrastructureLayer}\Repository\{RepositoryName}:
        arguments:
            $entityManager: '@doctrine.orm.entity_manager'
```

### 2. Интерфейсы
```php
<?php

declare(strict_types=1);

namespace App\Module\{ModuleName}\Contract;

/**
 * Интерфейс для работы с {DomainConcept}
 */
interface {InterfaceName}
{
    /**
     * Описание метода
     * 
     * @param {ParamType} ${paramName} описание параметра
     * @return {ReturnType} описание возвращаемого значения
     */
    public function {methodName}({ParamType} ${paramName}): {ReturnType};
}
```

## Производительность

### 1. Кэширование
```php
// Использование Redis для кэширования
final readonly class Cached{RepositoryName} implements {RepositoryName}Interface
{
    public function __construct(
        private {RepositoryName}Interface $repository,
        private RedisInterface $redis,
    ) {
    }

    public function findById(string $id): ?{ValueObjectName}
    {
        $cacheKey = "{entity}:{$id}";
        
        $cached = $this->redis->get($cacheKey);
        if ($cached !== null) {
            return unserialize($cached);
        }

        $entity = $this->repository->findById($id);
        if ($entity !== null) {
            $this->redis->setex($cacheKey, 3600, serialize($entity));
        }

        return $entity;
    }
}
```

### 2. Оптимизация запросов
```php
// Использование QueryBuilder для оптимизации
public function findByCriteria(array $criteria): array
{
    $qb = $this->entityManager->createQueryBuilder();
    $qb->select('e')
       ->from({ModelName}::class, 'e');

    foreach ($criteria as $field => $value) {
        $qb->andWhere("e.{$field} = :{$field}")
           ->setParameter($field, $value);
    }

    return array_map(
        fn($model) => {ValueObjectName}::fromModel($model),
        $qb->getQuery()->getResult()
    );
}
```

## Безопасность

### 1. Валидация входных данных
```php
/**
 * Валидирует входные данные
 * 
 * @param array<string, mixed> $data данные для валидации
 * @throws {ValidationException} если данные невалидны
 */
private function validateInput(array $data): void
{
    if (empty($data['required_field'])) {
        throw new {ValidationException}('Required field is missing');
    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new {ValidationException}('Invalid email format');
    }
}
```

### 2. Санитизация данных
```php
/**
 * Санитизирует строковые данные
 * 
 * @param string $input входные данные
 * @return string очищенные данные
 */
private function sanitizeString(string $input): string
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}
```

## CI/CD и качество кода

### 1. Composer scripts
```json
{
    "scripts": {
        "phpstan-full-scan": "./vendor/bin/phpstan analyse --memory-limit=2048M",
        "php-cs-fixer-fix": "php -d memory_limit=2048M ./vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php",
        "phpunit": "php -d memory_limit=2048M ./vendor/bin/phpunit --no-coverage",
        "lint": [
            "@php-cs-fixer-fix",
            "@phpstan-full-scan",
            "@phpunit"
        ]
    }
}
```

### 2. Проверки перед коммитом
```bash
# Запуск всех проверок
composer lint

# Отдельные проверки
composer php-cs-fixer-fix
composer phpstan-full-scan
composer phpunit
```

## Рекомендации по разработке

### 1. Рабочий процесс
1. **Планирование**: Определить требования и архитектуру
2. **Реализация**: Следовать шаблонам и стандартам
3. **Тестирование**: Написать unit и integration тесты
4. **Код-ревью**: Проверить соответствие стандартам
5. **Рефакторинг**: Улучшить код при необходимости

### 2. Принципы разработки
- **KISS**: Keep It Simple, Stupid
- **DRY**: Don't Repeat Yourself
- **YAGNI**: You Aren't Gonna Need It
- **SOLID**: Следовать принципам SOLID
- **Clean Code**: Писать чистый, читаемый код

### 3. Современные практики
- Использовать современные возможности PHP 8.2+
- Следовать PSR стандартам
- Применять функциональное программирование где это уместно
- Использовать типизацию везде, где это возможно
- Применять паттерны проектирования осознанно

## Заключение

Этот шаблон является основой для разработки качественного кода в проекте Sunday Research Bot. Следование этим стандартам обеспечивает:

- **Качество кода**: Чистый, читаемый и поддерживаемый код
- **Производительность**: Оптимизированные решения
- **Безопасность**: Защита от уязвимостей
- **Тестируемость**: Легко тестируемый код
- **Расширяемость**: Легко расширяемая архитектура
- **Совместимость**: Совместимость с современными стандартами

Все разработчики должны следовать этим стандартам для обеспечения консистентности и качества кодовой базы. 
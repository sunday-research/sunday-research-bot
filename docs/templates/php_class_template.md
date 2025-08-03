# Шаблон PHP класса

## Общий шаблон класса

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
        // другие зависимости...
    ) {
    }

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
}
```

## Шаблоны для разных типов классов

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

            // Вызов сервисов для выполнения операции
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
        // Валидация входных данных
        $this->validate{Entity}Data($dto);

        // Создание объекта
        $entity = {EntityName}::fromDTO($dto);

        // Сохранение через репозиторий
        $this->{repositoryName}->save($entity);

        return $entity;
    }

    /**
     * Находит {DomainConcept} по критериям
     * 
     * @param array<string, mixed> $criteria критерии поиска
     * @return {ValueObjectName}|null найденный объект или null
     */
    public function find{Entity}By(array $criteria): ?{ValueObjectName}
    {
        return $this->{repositoryName}->findBy($criteria);
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

    /**
     * Находит {DomainConcept} по критериям
     * 
     * @param array<string, mixed> $criteria критерии поиска
     * @return {ValueObjectName}|null найденный объект или null
     */
    public function findBy(array $criteria): ?{ValueObjectName}
    {
        $model = $this->entityManager->getRepository({ModelName}::class)
            ->findOneBy($criteria);

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

### 6. Entity класс
```php
<?php

declare(strict_types=1);

namespace App\Module\{ModuleName}\Entity;

use App\Module\{ModuleName}\ValueObject\{ValueObjectName};
use Ramsey\Uuid\UuidInterface;

/**
 * Доменная сущность {DomainConcept}
 */
final readonly class {EntityName}
{
    public function __construct(
        private UuidInterface $id,
        private {ValueObjectName} ${valueObjectName},
        private \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $updatedAt,
    ) {
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function get{ValueObjectName}(): {ValueObjectName}
    {
        return $this->{valueObjectName};
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Создает сущность из DTO
     * 
     * @param {DTOName} $dto данные для создания
     * @return self созданная сущность
     */
    public static function fromDTO({DTOName} $dto): self
    {
        return new self(
            id: \Ramsey\Uuid\Uuid::uuid4(),
            {valueObjectName}: new {ValueObjectName}($dto->getField1()),
            createdAt: new \DateTimeImmutable(),
            updatedAt: new \DateTimeImmutable(),
        );
    }

    /**
     * Обновляет сущность
     * 
     * @param {ValueObjectName} ${valueObjectName} новое значение
     * @return self обновленная сущность
     */
    public function update({ValueObjectName} ${valueObjectName}): self
    {
        return new self(
            id: $this->id,
            {valueObjectName}: ${valueObjectName},
            createdAt: $this->createdAt,
            updatedAt: new \DateTimeImmutable(),
        );
    }
}
```

### 7. Exception класс
```php
<?php

declare(strict_types=1);

namespace App\Module\{ModuleName}\Exception;

use Exception;

/**
 * Исключение для {ModuleName} модуля
 */
final class {ExceptionName} extends Exception
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

### 8. Factory класс
```php
<?php

declare(strict_types=1);

namespace App\Module\{ModuleName}\Factory;

use App\Module\{ModuleName}\Entity\{EntityName};
use App\Module\{ModuleName}\ValueObject\{ValueObjectName};

/**
 * Фабрика для создания {DomainConcept}
 */
final readonly class {FactoryName}
{
    /**
     * Создает {EntityName} из внешних данных
     * 
     * @param array<string, mixed> $data исходные данные
     * @return {EntityName} созданная сущность
     */
    public static function fromArray(array $data): {EntityName}
    {
        return new {EntityName}(
            id: \Ramsey\Uuid\Uuid::fromString($data['id']),
            {valueObjectName}: new {ValueObjectName}($data['value']),
            createdAt: new \DateTimeImmutable($data['created_at']),
            updatedAt: new \DateTimeImmutable($data['updated_at']),
        );
    }

    /**
     * Создает {EntityName} из внешнего API
     * 
     * @param {ExternalType} $externalData данные из внешнего API
     * @return {EntityName} созданная сущность
     */
    public static function fromExternalData({ExternalType} $externalData): {EntityName}
    {
        return new {EntityName}(
            id: \Ramsey\Uuid\Uuid::uuid4(),
            {valueObjectName}: new {ValueObjectName}($externalData->getValue()),
            createdAt: new \DateTimeImmutable(),
            updatedAt: new \DateTimeImmutable(),
        );
    }
}
```

## Правила использования шаблонов

1. **Всегда используйте `declare(strict_types=1);`**
2. **Всегда используйте `readonly` для неизменяемых классов**
3. **Всегда добавляйте типы параметров и возвращаемых значений**
4. **Используйте PHPDoc для публичных методов**
5. **Следуйте принципу единственной ответственности**
6. **Используйте dependency injection через конструктор**
7. **Обрабатывайте исключения на соответствующем уровне**
8. **Логируйте важные операции**
9. **Валидируйте входные данные**
10. **Используйте Value Objects для доменных концепций** 
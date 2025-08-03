# Шаблон тестов

## Общий шаблон теста

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
        
        // Инициализация объекта для тестирования
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

    /**
     * @return array<string, array<string, mixed>>
     */
    public function {methodName}_data_provider(): array
    {
        return [
            'valid case 1' => [
                'input' => 'valid input 1',
                'expected' => 'expected result 1',
            ],
            'valid case 2' => [
                'input' => 'valid input 2',
                'expected' => 'expected result 2',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider {methodName}_data_provider
     */
    public function it_should_{expected_behavior}_with_data_provider(string $input, string $expected): void
    {
        // Act
        $result = $this->{className}->{methodName}($input);

        // Assert
        $this->assertSame($expected, $result);
    }
}
```

## Шаблоны для разных типов тестов

### 1. Unit тест для Service
```php
<?php

declare(strict_types=1);

namespace App\Tests\Module\{ModuleName}\Service;

use App\Module\{ModuleName}\Service\{ServiceName};
use App\Module\{ModuleName}\Infrastructure\{InfrastructureLayer}\Repository\{RepositoryName};
use App\Module\{ModuleName}\DTO\{DTOName};
use App\Module\{ModuleName}\ValueObject\{ValueObjectName};
use App\Module\{ModuleName}\Exception\{ExceptionName};
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Unit тесты для {ServiceName}
 */
final class {ServiceName}Test extends TestCase
{
    private {ServiceName} ${serviceName};
    private {RepositoryName}&MockObject ${repositoryName};

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->{repositoryName} = $this->createMock({RepositoryName}::class);
        $this->{serviceName} = new {ServiceName}($this->{repositoryName});
    }

    /**
     * @test
     */
    public function it_should_create_{entity}_successfully(): void
    {
        // Arrange
        $dto = new {DTOName}(
            field1: 'test value',
            field2: 123,
        );
        $expectedValueObject = new {ValueObjectName}('test value');

        $this->{repositoryName}
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function ({ValueObjectName} $valueObject) {
                return $valueObject->getValue() === 'test value';
            }));

        // Act
        $result = $this->{serviceName}->create{Entity}($dto);

        // Assert
        $this->assertInstanceOf({ValueObjectName}::class, $result);
        $this->assertSame('test value', $result->getValue());
    }

    /**
     * @test
     */
    public function it_should_find_{entity}_by_criteria(): void
    {
        // Arrange
        $criteria = ['field' => 'value'];
        $expectedValueObject = new {ValueObjectName}('found value');

        $this->{repositoryName}
            ->expects($this->once())
            ->method('findBy')
            ->with($criteria)
            ->willReturn($expectedValueObject);

        // Act
        $result = $this->{serviceName}->find{Entity}By($criteria);

        // Assert
        $this->assertSame($expectedValueObject, $result);
    }

    /**
     * @test
     */
    public function it_should_return_null_when_{entity}_not_found(): void
    {
        // Arrange
        $criteria = ['field' => 'non_existent'];

        $this->{repositoryName}
            ->expects($this->once())
            ->method('findBy')
            ->with($criteria)
            ->willReturn(null);

        // Act
        $result = $this->{serviceName}->find{Entity}By($criteria);

        // Assert
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function it_should_throw_exception_when_creating_{entity}_with_invalid_data(): void
    {
        // Arrange
        $invalidDto = new {DTOName}(
            field1: '', // пустое значение
            field2: -1, // отрицательное значение
        );

        // Assert
        $this->expectException({ExceptionName}::class);
        $this->expectExceptionMessage('Invalid data provided');

        // Act
        $this->{serviceName}->create{Entity}($invalidDto);
    }
}
```

### 2. Unit тест для Repository
```php
<?php

declare(strict_types=1);

namespace App\Tests\Module\{ModuleName}\Infrastructure\{InfrastructureLayer}\Repository;

use App\Module\{ModuleName}\Infrastructure\{InfrastructureLayer}\Repository\{RepositoryName};
use App\Module\{ModuleName}\Infrastructure\{InfrastructureLayer}\Model\{ModelName};
use App\Module\{ModuleName}\ValueObject\{ValueObjectName};
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Unit тесты для {RepositoryName}
 */
final class {RepositoryName}Test extends TestCase
{
    private {RepositoryName} ${repositoryName};
    private EntityManagerInterface&MockObject $entityManager;
    private EntityRepository&MockObject $entityRepository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityRepository = $this->createMock(EntityRepository::class);
        $this->{repositoryName} = new {RepositoryName}($this->entityManager);
    }

    /**
     * @test
     */
    public function it_should_save_{entity}_successfully(): void
    {
        // Arrange
        $valueObject = new {ValueObjectName}('test value');

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf({ModelName}::class));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        // Act
        $this->{repositoryName}->save($valueObject);

        // Assert - проверяем, что методы были вызваны
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function it_should_find_{entity}_by_id(): void
    {
        // Arrange
        $id = 'test-id';
        $model = $this->createMock({ModelName}::class);
        $expectedValueObject = new {ValueObjectName}('test value');

        $model->method('toDomain')
            ->willReturn($expectedValueObject);

        $this->entityManager
            ->expects($this->once())
            ->method('find')
            ->with({ModelName}::class, $id)
            ->willReturn($model);

        // Act
        $result = $this->{repositoryName}->findById($id);

        // Assert
        $this->assertSame($expectedValueObject, $result);
    }

    /**
     * @test
     */
    public function it_should_return_null_when_{entity}_not_found_by_id(): void
    {
        // Arrange
        $id = 'non-existent-id';

        $this->entityManager
            ->expects($this->once())
            ->method('find')
            ->with({ModelName}::class, $id)
            ->willReturn(null);

        // Act
        $result = $this->{repositoryName}->findById($id);

        // Assert
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function it_should_find_{entity}_by_criteria(): void
    {
        // Arrange
        $criteria = ['field' => 'value'];
        $model = $this->createMock({ModelName}::class);
        $expectedValueObject = new {ValueObjectName}('found value');

        $model->method('toDomain')
            ->willReturn($expectedValueObject);

        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with({ModelName}::class)
            ->willReturn($this->entityRepository);

        $this->entityRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with($criteria)
            ->willReturn($model);

        // Act
        $result = $this->{repositoryName}->findBy($criteria);

        // Assert
        $this->assertSame($expectedValueObject, $result);
    }
}
```

### 3. Unit тест для DTO
```php
<?php

declare(strict_types=1);

namespace App\Tests\Module\{ModuleName}\DTO;

use App\Module\{ModuleName}\DTO\{DTOName};
use PHPUnit\Framework\TestCase;

/**
 * Unit тесты для {DTOName}
 */
final class {DTOName}Test extends TestCase
{
    /**
     * @test
     */
    public function it_should_create_dto_with_required_fields(): void
    {
        // Arrange & Act
        $dto = new {DTOName}(
            field1: 'test value',
            field2: 123,
        );

        // Assert
        $this->assertSame('test value', $dto->getField1());
        $this->assertSame(123, $dto->getField2());
        $this->assertNull($dto->getOptionalField());
    }

    /**
     * @test
     */
    public function it_should_create_dto_with_optional_field(): void
    {
        // Arrange & Act
        $dto = new {DTOName}(
            field1: 'test value',
            field2: 123,
            optionalField: 'optional value',
        );

        // Assert
        $this->assertSame('test value', $dto->getField1());
        $this->assertSame(123, $dto->getField2());
        $this->assertSame('optional value', $dto->getOptionalField());
    }

    /**
     * @test
     */
    public function it_should_create_dto_from_array(): void
    {
        // Arrange
        $data = [
            'field1' => 'test value',
            'field2' => 123,
            'optional_field' => 'optional value',
        ];

        // Act
        $dto = {DTOName}::fromArray($data);

        // Assert
        $this->assertSame('test value', $dto->getField1());
        $this->assertSame(123, $dto->getField2());
        $this->assertSame('optional value', $dto->getOptionalField());
    }

    /**
     * @test
     */
    public function it_should_create_dto_from_array_without_optional_field(): void
    {
        // Arrange
        $data = [
            'field1' => 'test value',
            'field2' => 123,
        ];

        // Act
        $dto = {DTOName}::fromArray($data);

        // Assert
        $this->assertSame('test value', $dto->getField1());
        $this->assertSame(123, $dto->getField2());
        $this->assertNull($dto->getOptionalField());
    }

    /**
     * @test
     */
    public function it_should_convert_dto_to_array(): void
    {
        // Arrange
        $dto = new {DTOName}(
            field1: 'test value',
            field2: 123,
            optionalField: 'optional value',
        );

        // Act
        $result = $dto->toArray();

        // Assert
        $this->assertSame([
            'field1' => 'test value',
            'field2' => 123,
            'optional_field' => 'optional value',
        ], $result);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function from_array_data_provider(): array
    {
        return [
            'with all fields' => [
                'data' => [
                    'field1' => 'value1',
                    'field2' => 456,
                    'optional_field' => 'optional',
                ],
                'expected_field1' => 'value1',
                'expected_field2' => 456,
                'expected_optional' => 'optional',
            ],
            'without optional field' => [
                'data' => [
                    'field1' => 'value2',
                    'field2' => 789,
                ],
                'expected_field1' => 'value2',
                'expected_field2' => 789,
                'expected_optional' => null,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider from_array_data_provider
     */
    public function it_should_create_dto_from_array_with_data_provider(
        array $data,
        string $expectedField1,
        int $expectedField2,
        ?string $expectedOptional
    ): void {
        // Act
        $dto = {DTOName}::fromArray($data);

        // Assert
        $this->assertSame($expectedField1, $dto->getField1());
        $this->assertSame($expectedField2, $dto->getField2());
        $this->assertSame($expectedOptional, $dto->getOptionalField());
    }
}
```

### 4. Unit тест для Value Object
```php
<?php

declare(strict_types=1);

namespace App\Tests\Module\{ModuleName}\ValueObject;

use App\Module\{ModuleName}\ValueObject\{ValueObjectName};
use App\Module\{ModuleName}\Exception\{ValidationException};
use PHPUnit\Framework\TestCase;

/**
 * Unit тесты для {ValueObjectName}
 */
final class {ValueObjectName}Test extends TestCase
{
    /**
     * @test
     */
    public function it_should_create_value_object_with_valid_value(): void
    {
        // Arrange & Act
        $valueObject = new {ValueObjectName}('valid value');

        // Assert
        $this->assertSame('valid value', $valueObject->getValue());
    }

    /**
     * @test
     */
    public function it_should_throw_exception_when_value_is_empty(): void
    {
        // Assert
        $this->expectException({ValidationException}::class);
        $this->expectExceptionMessage('Value cannot be empty');

        // Act
        new {ValueObjectName}('');
    }

    /**
     * @test
     */
    public function it_should_throw_exception_when_value_is_whitespace_only(): void
    {
        // Assert
        $this->expectException({ValidationException}::class);
        $this->expectExceptionMessage('Value cannot be empty');

        // Act
        new {ValueObjectName}('   ');
    }

    /**
     * @test
     */
    public function it_should_be_equal_to_identical_value_object(): void
    {
        // Arrange
        $valueObject1 = new {ValueObjectName}('test value');
        $valueObject2 = new {ValueObjectName}('test value');

        // Act & Assert
        $this->assertTrue($valueObject1->equals($valueObject2));
        $this->assertTrue($valueObject2->equals($valueObject1));
    }

    /**
     * @test
     */
    public function it_should_not_be_equal_to_different_value_object(): void
    {
        // Arrange
        $valueObject1 = new {ValueObjectName}('test value 1');
        $valueObject2 = new {ValueObjectName}('test value 2');

        // Act & Assert
        $this->assertFalse($valueObject1->equals($valueObject2));
        $this->assertFalse($valueObject2->equals($valueObject1));
    }

    /**
     * @test
     */
    public function it_should_create_from_string(): void
    {
        // Arrange & Act
        $valueObject = {ValueObjectName}::fromString('test value');

        // Assert
        $this->assertInstanceOf({ValueObjectName}::class, $valueObject);
        $this->assertSame('test value', $valueObject->getValue());
    }

    /**
     * @test
     */
    public function it_should_convert_to_string(): void
    {
        // Arrange
        $valueObject = new {ValueObjectName}('test value');

        // Act
        $result = (string) $valueObject;

        // Assert
        $this->assertSame('test value', $result);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function validation_data_provider(): array
    {
        return [
            'empty string' => [
                'value' => '',
                'should_throw' => true,
            ],
            'whitespace only' => [
                'value' => '   ',
                'should_throw' => true,
            ],
            'valid value' => [
                'value' => 'valid value',
                'should_throw' => false,
            ],
            'single character' => [
                'value' => 'a',
                'should_throw' => false,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider validation_data_provider
     */
    public function it_should_validate_value_correctly(string $value, bool $shouldThrow): void
    {
        if ($shouldThrow) {
            $this->expectException({ValidationException}::class);
            $this->expectExceptionMessage('Value cannot be empty');
        }

        // Act
        $valueObject = new {ValueObjectName}($value);

        // Assert (only if no exception was thrown)
        if (!$shouldThrow) {
            $this->assertSame($value, $valueObject->getValue());
        }
    }
}
```

### 5. Integration тест
```php
<?php

declare(strict_types=1);

namespace App\Tests\Module\{ModuleName}\Integration;

use App\Module\{ModuleName}\{ModuleName}Manager;
use App\Module\{ModuleName}\Service\{ServiceName};
use App\Module\{ModuleName}\Infrastructure\{InfrastructureLayer}\Repository\{RepositoryName};
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Psr\Log\LoggerInterface;

/**
 * Integration тесты для {ModuleName}Manager
 */
final class {ModuleName}ManagerIntegrationTest extends KernelTestCase
{
    private {ModuleName}Manager ${moduleName}Manager;
    private {ServiceName} ${serviceName};
    private {RepositoryName} ${repositoryName};
    private LoggerInterface $logger;

    protected function setUp(): void
    {
        parent::setUp();
        
        $container = static::getContainer();
        
        $this->{serviceName} = $container->get({ServiceName}::class);
        $this->{repositoryName} = $container->get({RepositoryName}::class);
        $this->logger = $container->get(LoggerInterface::class);
        
        $this->{moduleName}Manager = new {ModuleName}Manager(
            $this->{serviceName},
            $this->logger,
        );
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
        // Проверяем, что данные были сохранены в БД
        $savedEntity = $this->{repositoryName}->findBy(['field' => 'expected_value']);
        $this->assertNotNull($savedEntity);
        
        // Дополнительные проверки...
    }

    /**
     * @test
     */
    public function it_should_handle_errors_gracefully(): void
    {
        // Arrange
        $invalidInput = 'invalid input that will cause error';

        // Act & Assert
        $this->expectException(\Exception::class);
        
        $this->{moduleName}Manager->execute{Operation}($invalidInput);
    }
}
```

## Правила написания тестов

### 1. Общие принципы
- **AAA Pattern**: Arrange, Act, Assert
- **Один тест - одна проверка**: Каждый тест должен проверять только одно поведение
- **Читаемые имена**: Имена тестов должны описывать ожидаемое поведение
- **Изоляция**: Тесты не должны зависеть друг от друга

### 2. Моки и стабы
- **Используйте моки для внешних зависимостей**
- **Стабы для возвращения предопределенных значений**
- **Проверяйте вызовы методов с правильными параметрами**

### 3. Data Providers
- **Используйте для тестирования множественных сценариев**
- **Документируйте каждый сценарий в массиве**
- **Включайте как позитивные, так и негативные случаи**

### 4. Исключения
- **Тестируйте исключения отдельно**
- **Проверяйте тип и сообщение исключения**
- **Используйте `expectException()` и `expectExceptionMessage()`**

### 5. Интеграционные тесты
- **Используйте `KernelTestCase` для интеграционных тестов**
- **Тестируйте полный путь выполнения**
- **Проверяйте взаимодействие с реальными сервисами**

### 6. Покрытие кода
- **Стремитесь к 100% покрытию**
- **Тестируйте граничные случаи**
- **Включайте тесты для обработки ошибок**

### 7. Производительность
- **Используйте `@group` для группировки тестов**
- **Запускайте быстрые тесты отдельно от медленных**
- **Используйте `@depends` для зависимых тестов**

### 8. Документация
- **Добавляйте комментарии к сложным тестам**
- **Используйте PHPDoc для data providers**
- **Документируйте настройку тестового окружения** 
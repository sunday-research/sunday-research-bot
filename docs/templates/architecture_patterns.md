# Архитектурные паттерны и принципы

## 1. Domain-Driven Design (DDD)

### 1.1 Слои архитектуры

```
┌─────────────────────────────────────┐
│           Presentation Layer        │
│  (Controllers, CLI Commands)        │
├─────────────────────────────────────┤
│           Application Layer         │
│  (Managers, Use Cases)              │
├─────────────────────────────────────┤
│            Domain Layer             │
│  (Entities, Value Objects, Services)│
├─────────────────────────────────────┤
│         Infrastructure Layer        │
│  (Repositories, External APIs)      │
└─────────────────────────────────────┘
```

### 1.2 Принципы DDD

#### Bounded Context
- Каждый модуль представляет отдельный bounded context
- Четкие границы между модулями
- Минимальная связанность между контекстами

#### Ubiquitous Language
- Использование терминологии домена в коде
- Согласованность между кодом и бизнес-терминами
- Документирование доменных концепций

#### Strategic Design
- Context Map для отображения связей между модулями
- Anti-Corruption Layer для внешних зависимостей
- Shared Kernel для общих концепций

## 2. Clean Architecture

### 2.1 Принципы

#### Dependency Rule
```
Внешние слои зависят от внутренних
Внутренние слои НЕ зависят от внешних
```

#### Dependency Inversion
```php
// ❌ Плохо - зависимость от конкретной реализации
class UserService
{
    public function __construct(
        private PostgresUserRepository $repository
    ) {}
}

// ✅ Хорошо - зависимость от абстракции
class UserService
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}
}
```

### 2.2 Структура слоев

#### Domain Layer (Внутренний)
```php
// Entities
final readonly class User
{
    public function __construct(
        private UuidInterface $id,
        private Email $email,
        private UserStatus $status,
    ) {}
}

// Value Objects
final readonly class Email
{
    public function __construct(private string $value)
    {
        $this->validate($value);
    }
}

// Domain Services
final readonly class UserRegistrationService
{
    public function registerUser(RegistrationData $data): User
    {
        // Бизнес-логика регистрации
    }
}
```

#### Application Layer
```php
// Use Cases / Managers
final readonly class UserManager
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserRegistrationService $registrationService,
        private LoggerInterface $logger,
    ) {}

    public function registerUser(RegisterUserDTO $dto): User
    {
        try {
            $user = $this->registrationService->registerUser($dto->toDomain());
            $this->userRepository->save($user);
            
            $this->logger->info('User registered successfully', [
                'user_id' => $user->getId()->toString()
            ]);
            
            return $user;
        } catch (Exception $e) {
            $this->logger->error('Failed to register user', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
```

#### Infrastructure Layer
```php
// Repository Implementation
final readonly class DoctrineUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function save(User $user): void
    {
        $model = UserModel::fromDomain($user);
        $this->entityManager->persist($model);
        $this->entityManager->flush();
    }
}
```

## 3. SOLID принципы

### 3.1 Single Responsibility Principle (SRP)

```php
// ❌ Плохо - класс делает слишком много
class UserManager
{
    public function registerUser(): void {}
    public function sendEmail(): void {}
    public function validateData(): void {}
    public function saveToDatabase(): void {}
}

// ✅ Хорошо - каждый класс имеет одну ответственность
final readonly class UserRegistrationService
{
    public function registerUser(RegistrationData $data): User {}
}

final readonly class EmailService
{
    public function sendWelcomeEmail(User $user): void {}
}

final readonly class UserValidator
{
    public function validate(RegistrationData $data): ValidationResult {}
}
```

### 3.2 Open/Closed Principle (OCP)

```php
// ✅ Хорошо - открыт для расширения, закрыт для модификации
interface PaymentProcessorInterface
{
    public function process(Payment $payment): PaymentResult;
}

final readonly class CreditCardProcessor implements PaymentProcessorInterface
{
    public function process(Payment $payment): PaymentResult {}
}

final readonly class PayPalProcessor implements PaymentProcessorInterface
{
    public function process(Payment $payment): PaymentResult {}
}

final readonly class PaymentService
{
    public function __construct(
        private PaymentProcessorInterface $processor
    ) {}
}
```

### 3.3 Liskov Substitution Principle (LSP)

```php
// ✅ Хорошо - подклассы могут заменять базовые классы
interface UserRepositoryInterface
{
    public function findById(string $id): ?User;
    public function save(User $user): void;
}

final readonly class PostgresUserRepository implements UserRepositoryInterface
{
    public function findById(string $id): ?User {}
    public function save(User $user): void {}
}

final readonly class RedisUserRepository implements UserRepositoryInterface
{
    public function findById(string $id): ?User {}
    public function save(User $user): void {}
}
```

### 3.4 Interface Segregation Principle (ISP)

```php
// ❌ Плохо - большой интерфейс
interface UserRepositoryInterface
{
    public function findById(string $id): ?User;
    public function save(User $user): void;
    public function delete(User $user): void;
    public function update(User $user): void;
    public function findByEmail(string $email): ?User;
    public function findByStatus(UserStatus $status): array;
}

// ✅ Хорошо - разделение на специализированные интерфейсы
interface UserReaderInterface
{
    public function findById(string $id): ?User;
    public function findByEmail(string $email): ?User;
    public function findByStatus(UserStatus $status): array;
}

interface UserWriterInterface
{
    public function save(User $user): void;
    public function delete(User $user): void;
    public function update(User $user): void;
}

interface UserRepositoryInterface extends UserReaderInterface, UserWriterInterface {}
```

### 3.5 Dependency Inversion Principle (DIP)

```php
// ✅ Хорошо - зависимости от абстракций
final readonly class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private EmailServiceInterface $emailService,
        private LoggerInterface $logger,
    ) {}
}
```

## 4. Паттерны проектирования

### 4.1 Repository Pattern

```php
// Интерфейс репозитория
interface UserRepositoryInterface
{
    public function findById(string $id): ?User;
    public function save(User $user): void;
    public function delete(User $user): void;
    public function findByCriteria(Criteria $criteria): array;
}

// Реализация для Doctrine
final readonly class DoctrineUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function findById(string $id): ?User
    {
        $model = $this->entityManager->find(UserModel::class, $id);
        return $model ? User::fromModel($model) : null;
    }

    public function save(User $user): void
    {
        $model = UserModel::fromDomain($user);
        $this->entityManager->persist($model);
        $this->entityManager->flush();
    }
}
```

### 4.2 Factory Pattern

```php
// Фабрика для создания объектов
final readonly class UserFactory
{
    public static function createFromArray(array $data): User
    {
        return new User(
            id: Uuid::fromString($data['id']),
            email: new Email($data['email']),
            status: UserStatus::fromString($data['status']),
        );
    }

    public static function createFromExternalApi(ExternalUserData $data): User
    {
        return new User(
            id: Uuid::uuid4(),
            email: new Email($data->getEmail()),
            status: UserStatus::ACTIVE,
        );
    }
}
```

### 4.3 Strategy Pattern

```php
// Стратегии для разных типов валидации
interface ValidationStrategyInterface
{
    public function validate(mixed $data): ValidationResult;
}

final readonly class EmailValidationStrategy implements ValidationStrategyInterface
{
    public function validate(mixed $data): ValidationResult
    {
        // Валидация email
    }
}

final readonly class PasswordValidationStrategy implements ValidationStrategyInterface
{
    public function validate(mixed $data): ValidationResult
    {
        // Валидация пароля
    }
}

final readonly class Validator
{
    public function __construct(
        private ValidationStrategyInterface $strategy
    ) {}

    public function validate(mixed $data): ValidationResult
    {
        return $this->strategy->validate($data);
    }
}
```

### 4.4 Observer Pattern

```php
// События домена
interface DomainEventInterface
{
    public function occurredOn(): DateTimeImmutable;
}

final readonly class UserRegisteredEvent implements DomainEventInterface
{
    public function __construct(
        private User $user,
        private DateTimeImmutable $occurredOn,
    ) {}

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }
}

// Обработчики событий
interface EventHandlerInterface
{
    public function handle(DomainEventInterface $event): void;
}

final readonly class SendWelcomeEmailHandler implements EventHandlerInterface
{
    public function handle(DomainEventInterface $event): void
    {
        if ($event instanceof UserRegisteredEvent) {
            // Отправка приветственного email
        }
    }
}
```

## 5. Модульная архитектура

### 5.1 Структура модуля

```
Module/
├── Contract/           # Интерфейсы и контракты
├── DTO/               # Data Transfer Objects
├── Entity/            # Доменные сущности
├── Enum/              # Перечисления
├── Exception/         # Исключения модуля
├── Factory/           # Фабрики
├── Handler/           # Обработчики
├── Infrastructure/    # Инфраструктурный слой
├── Message/           # Сообщения для Messenger
├── MessageHandler/    # Обработчики сообщений
├── Service/           # Сервисы бизнес-логики
└── ValueObject/       # Объекты-значения
```

### 5.2 Принципы модульности

#### Инкапсуляция
- Модули скрывают внутреннюю реализацию
- Публичный API через интерфейсы
- Минимальная связанность между модулями

#### Слабая связанность
```php
// ❌ Плохо - сильная связанность
class UserService
{
    public function __construct(
        private PostgresUserRepository $userRepository,
        private PostgresOrderRepository $orderRepository,
    ) {}
}

// ✅ Хорошо - слабая связанность через интерфейсы
class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private OrderRepositoryInterface $orderRepository,
    ) {}
}
```

#### Высокая сплоченность
```php
// ✅ Хорошо - связанные функции в одном модуле
final readonly class UserModule
{
    public function __construct(
        private UserRegistrationService $registrationService,
        private UserValidationService $validationService,
        private UserNotificationService $notificationService,
    ) {}
}
```

## 6. Паттерны обработки ошибок

### 6.1 Иерархия исключений

```php
// Базовое исключение модуля
abstract class ModuleException extends Exception
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Exception $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}

// Специфичные исключения
final class UserNotFoundException extends ModuleException {}
final class UserValidationException extends ModuleException {}
final class UserRegistrationException extends ModuleException {}
```

### 6.2 Result Pattern

```php
// Паттерн Result для обработки ошибок
final readonly class Result<T>
{
    private function __construct(
        private mixed $value = null,
        private ?Exception $error = null,
    ) {}

    public static function success(mixed $value): self
    {
        return new self(value: $value);
    }

    public static function failure(Exception $error): self
    {
        return new self(error: $error);
    }

    public function isSuccess(): bool
    {
        return $this->error === null;
    }

    public function getValue(): mixed
    {
        if (!$this->isSuccess()) {
            throw new RuntimeException('Cannot get value from failed result');
        }
        return $this->value;
    }

    public function getError(): ?Exception
    {
        return $this->error;
    }
}
```

## 7. Паттерны для производительности

### 7.1 Caching Pattern

```php
// Кэширование с интерфейсом
interface CacheInterface
{
    public function get(string $key): mixed;
    public function set(string $key, mixed $value, int $ttl = 3600): void;
    public function delete(string $key): void;
}

final readonly class CachedUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private UserRepositoryInterface $repository,
        private CacheInterface $cache,
    ) {}

    public function findById(string $id): ?User
    {
        $cacheKey = "user:{$id}";
        
        $cached = $this->cache->get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $user = $this->repository->findById($id);
        if ($user !== null) {
            $this->cache->set($cacheKey, $user, 3600);
        }

        return $user;
    }
}
```

### 7.2 Lazy Loading

```php
// Ленивая загрузка
final readonly class LazyUserService
{
    private ?UserRepositoryInterface $repository = null;

    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    private function getRepository(): UserRepositoryInterface
    {
        if ($this->repository === null) {
            $this->repository = new DoctrineUserRepository($this->entityManager);
        }
        return $this->repository;
    }
}
```

## 8. Паттерны для тестирования

### 8.1 Test Doubles

```php
// Моки для тестирования
final readonly class MockUserRepository implements UserRepositoryInterface
{
    private array $users = [];

    public function findById(string $id): ?User
    {
        return $this->users[$id] ?? null;
    }

    public function save(User $user): void
    {
        $this->users[$user->getId()->toString()] = $user;
    }

    public function delete(User $user): void
    {
        unset($this->users[$user->getId()->toString()]);
    }
}
```

### 8.2 Test Data Builders

```php
// Билдеры для тестовых данных
final readonly class UserBuilder
{
    private UuidInterface $id;
    private Email $email;
    private UserStatus $status;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->email = new Email('test@example.com');
        $this->status = UserStatus::ACTIVE;
    }

    public function withId(UuidInterface $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function withEmail(Email $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function build(): User
    {
        return new User($this->id, $this->email, $this->status);
    }
}
```

## 9. Рекомендации по применению

### 9.1 Когда использовать паттерны

- **Repository Pattern**: Для абстракции доступа к данным
- **Factory Pattern**: Для сложного создания объектов
- **Strategy Pattern**: Для альтернативных алгоритмов
- **Observer Pattern**: Для событийно-ориентированной архитектуры
- **Result Pattern**: Для функционального подхода к ошибкам

### 9.2 Антипаттерны

- **God Object**: Класс, который делает слишком много
- **Anemic Domain Model**: Сущности без бизнес-логики
- **Tight Coupling**: Сильная связанность между компонентами
- **Premature Optimization**: Преждевременная оптимизация

### 9.3 Принципы выбора

1. **Простота**: Выбирайте простейшее решение
2. **Читаемость**: Код должен быть понятным
3. **Тестируемость**: Легко тестировать
4. **Расширяемость**: Легко расширять
5. **Производительность**: Учитывайте производительность 
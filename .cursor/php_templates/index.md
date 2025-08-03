# PHP Templates Index

## Overview
This directory contains PHP templates for the Sunday Research Bot project, designed to ensure consistent code quality and architecture across the codebase.

## Standards
- **PHP Version**: 8.2.9+
- **Symfony**: 7.0+
- **PHPStan**: Level 10 (maximum)
- **PHP-CS-Fixer**: PSR-12
- **Architecture**: Clean Architecture, DDD, SOLID principles

## Available Templates

### Core Classes

#### 1. **modern_php_class.template**
- **ID**: `modern_php_class`
- **Scope**: General PHP class
- **Requirements**: strict_types=1, readonly_classes=1, dependency_injection=1
- **Use Case**: Base template for any PHP class

#### 2. **manager_class.template**
- **ID**: `manager_class`
- **Scope**: High-level business logic manager
- **Requirements**: logging=1
- **Use Case**: Orchestrates business operations and coordinates services

#### 3. **service_class.template**
- **ID**: `service_class`
- **Scope**: Business logic service
- **Requirements**: validation=1
- **Use Case**: Handles specific business operations and coordinates with repositories

#### 4. **repository_class.template**
- **ID**: `repository_class`
- **Scope**: Data persistence layer
- **Requirements**: doctrine_orm=1
- **Use Case**: Handles data access and mapping between domain and infrastructure

### Data Objects

#### 5. **dto_class.template**
- **ID**: `dto_class`
- **Scope**: Data Transfer Objects
- **Requirements**: immutable_objects=1
- **Use Case**: Immutable objects for data exchange between layers

#### 6. **value_object_class.template**
- **ID**: `value_object_class`
- **Scope**: Domain Value Objects
- **Requirements**: validation=1, equals_method=1
- **Use Case**: Immutable objects representing domain concepts

#### 7. **entity_class.template**
- **ID**: `entity_class`
- **Scope**: Domain Entities
- **Requirements**: uuid=1, timestamps=1
- **Use Case**: Business objects with identity and lifecycle

### Contracts & Exceptions

#### 8. **interface_class.template**
- **ID**: `interface_class`
- **Scope**: Interfaces and contracts
- **Requirements**: contract_design=1, dependency_inversion=1
- **Use Case**: Defines contracts for dependency inversion

#### 9. **exception_class.template**
- **ID**: `exception_class`
- **Scope**: Module exceptions
- **Requirements**: exception_hierarchy=1, context_support=1
- **Use Case**: Module-specific error handling with context

### Utilities

#### 10. **enum_class.template**
- **ID**: `enum_class`
- **Scope**: PHP 8.1+ Enums
- **Requirements**: php_enum=1, value_objects=1
- **Use Case**: Fixed set of values for domain concepts

#### 11. **cli_command.template**
- **ID**: `cli_command`
- **Scope**: Symfony Console Commands
- **Requirements**: symfony_console=1
- **Use Case**: Command-line interface for operations

### Testing

#### 12. **unit_test.template**
- **ID**: `unit_test`
- **Scope**: Unit tests
- **Requirements**: unit_testing=1, mocking=1, aaa_pattern=1
- **Use Case**: Testing individual components in isolation

#### 13. **integration_test.template**
- **ID**: `integration_test`
- **Scope**: Integration tests
- **Requirements**: integration_testing=1, kernel_testcase=1, container=1
- **Use Case**: Testing component interactions and full workflows

## Usage

### Template Selection
Choose the appropriate template based on:
1. **Class Type**: Manager, Service, Repository, etc.
2. **Requirements**: Specific needs (logging, validation, etc.)
3. **Architecture Layer**: Domain, Application, Infrastructure

### Template Variables
All templates use placeholder variables in `{VariableName}` format:
- `{ModuleName}`: Module name (e.g., User, Order, Payment)
- `{ClassName}`: Class name (e.g., UserService, OrderRepository)
- `{Layer}`: Architecture layer (e.g., Service, Repository, DTO)
- `{DomainConcept}`: Domain concept being modeled
- `{InfrastructureLayer}`: Infrastructure layer (e.g., Doctrine, Redis, Telegram)

### Best Practices
1. **Always use strict_types=1**
2. **Use readonly classes where appropriate**
3. **Implement proper dependency injection**
4. **Add comprehensive PHPDoc**
5. **Follow SOLID principles**
6. **Write tests for all classes**

## Architecture Guidelines

### Module Structure
```
src/Module/{ModuleName}/
├── Contract/           # Interfaces and contracts
├── DTO/               # Data Transfer Objects
├── Entity/            # Domain entities
├── Enum/              # Enumerations
├── Exception/         # Module exceptions
├── Factory/           # Factories
├── Handler/           # Event/command handlers
├── Infrastructure/    # Infrastructure layer
│   ├── Doctrine/      # Database operations
│   ├── Redis/         # Cache operations
│   └── Telegram/      # External API operations
├── Message/           # Messenger messages
├── MessageHandler/    # Message handlers
├── Service/           # Business logic services
└── ValueObject/       # Value objects
```

### Call Hierarchy
```
CLI/Controller → Manager → Service → Repository
```

### Testing Structure
```
tests/Module/{ModuleName}/
├── {Layer}/           # Unit tests
└── Integration/       # Integration tests
```

## Quality Assurance

### Static Analysis
- **PHPStan Level 10**: Maximum static analysis
- **PHP-CS-Fixer**: PSR-12 code formatting
- **Strict Types**: All files must use `declare(strict_types=1);`

### Testing Requirements
- **Unit Tests**: 100% coverage for business logic
- **Integration Tests**: End-to-end workflow testing
- **AAA Pattern**: Arrange, Act, Assert
- **Mocking**: Use mocks for external dependencies

### Code Quality
- **SOLID Principles**: Strict adherence
- **Clean Architecture**: Clear layer separation
- **DDD**: Domain-driven design patterns
- **Immutable Objects**: Use readonly classes
- **Type Safety**: Comprehensive type hints 
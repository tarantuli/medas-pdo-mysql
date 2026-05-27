# medas-pdo-mysql

Part of the [Medas framework](https://github.com/tarantuli/medas-core).

## Description

The MySQL driver for `medas-pdo-storage`. It registers a `MysqlHandler` with `DriverHandlerManager` during package startup, enabling `medas-storage-manager` and `medas-entity-manager` to use MySQL databases as storage backends.

`MysqlHandler` implements `DriverHandler` and handles all `mysql`-driver DSN connections. It provides:

- Backtick identifier quoting
- `PDO::quote()`-based value escaping with `null`, `bool`, and `BackedEnum` normalization
- MySQL-specific query builders (INSERT, UPDATE, DELETE, SELECT, DROP TABLE, SHOW TABLES, collection update)
- MySQL errno → `StorageExceptionType` translation:

| MySQL errno      | `StorageExceptionType` |
|------------------|------------------------|
| 1062             | `DuplicateKey`         |
| 1216, 1217, 1452 | `ForeignKeyViolation`  |
| 1213             | `DeadlockDetected`     |
| 1205             | `LockWaitTimeout`      |
| 2006, 2013       | `ConnectionLost`       |
| other            | `Unknown`              |

The actual PDO connection, query execution, schema migration, and selector translation are all handled by `medas-pdo-storage`. This package is purely the MySQL dialect layer.

## Usage

### Package developer context

Register the package — it registers the MySQL driver handler automatically:

```php
use Medas\PdoMysql\PdoMysqlPackage;

PdoMysqlPackage::instance();
```

**Configuring a MySQL database connection:**

The connection is defined by a `Database` value object from `medas-pdo-storage`, resolved from config values:

```yaml
pdo:
  dsn: "mysql:host=127.0.0.1;port=3306;dbname=my_app;charset=utf8mb4"
  username: $env(DB_USERNAME)
  password: $env(DB_PASSWORD)
  name: default
  persistent-connection: false
```

```
DB_USERNAME=app_user
DB_PASSWORD=secret
```

The `name` field is used to reference this connection from entity definitions. For a single-database application the name `default` is conventional; the entity manager will use it automatically when no `storage` is specified in `#[Entity]`.

**Multiple database connections:**

Register additional `Database` instances with distinct names to route different entity classes to different databases:

```yaml
# config/database.yaml
pdo:
  dsn: "mysql:host=127.0.0.1;dbname=main_app;charset=utf8mb4"
  username: $env(DB_USERNAME)
  password: $env(DB_PASSWORD)
  name: default
  persistent-connection: false

pdo-audit:
  dsn: "mysql:host=audit-db.internal;dbname=audit_log;charset=utf8mb4"
  username: $env(AUDIT_DB_USERNAME)
  password: $env(AUDIT_DB_PASSWORD)
  name: audit
  persistent-connection: false
```

Then reference the named connection in entity definitions:

```php
use Medas\EntityManager\Attributes\{Entity, Id};
use Medas\Core\Interfaces\{HasId, Uuid};
use Medas\EntityManager\Traits\Timestamps;

// Uses the 'default' connection
#[Entity(store: 'invoices')]
class Invoice implements HasId
{
    use Timestamps;

    #[Id]
    public Uuid $id;

    public string $status;

    public function id(): Uuid { return $this->id; }
}

// Uses the 'audit' connection
#[Entity(store: 'change_events', storage: 'audit')]
class ChangeEvent implements HasId
{
    use Timestamps;

    #[Id]
    public Uuid $id;

    public string $entityClass;
    public string $entityId;

    public function id(): Uuid { return $this->id; }
}
```

**Handling MySQL-specific exceptions:**

`StorageExceptionType` is set on every `StorageException` thrown by the entity manager. Use it to distinguish duplicate key violations from connection failures:

```php
use Medas\Core\Exceptions\{StorageException, StorageExceptionType};

try {
    $this->entityManager->persist($invoice);
    $this->entityManager->flush();
} catch (StorageException $e) {
    match ($e->exceptionType) {
        StorageExceptionType::DuplicateKey      => $this->handleDuplicate($invoice),
        StorageExceptionType::ConnectionLost    => $this->reconnectAndRetry(),
        StorageExceptionType::DeadlockDetected  => $this->retryTransaction(),
        default => throw $e,
    };
}
```

### Backend user context

**Connection string format:**

```
mysql:host=<host>;port=<port>;dbname=<database>;charset=utf8mb4
```

Always include `charset=utf8mb4` to support the full Unicode range (including emoji). Omitting it defaults to `latin1` in older MySQL versions, which silently truncates \multibyte characters.

**Persistent connections** — set `persistent-connection: true` to reuse PDO connections across PHP-FPM worker requests. This reduces connection overhead but can cause issues with transactions left open by a previous request. Use with care and only when profiling confirms `connection overhead is significant.

**Schema management** — `medas-pdo-storage` generates `CREATE TABLE` and `ALTER TABLE` statements from entity attribute definitions. Tables are created or migrated automatically when the entity manager first accesses a store. No separate migration tool is required for typical add-column changes; destructive changes (column renames, type changes) require manual intervention.

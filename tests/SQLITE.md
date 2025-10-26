# SQLite Testing Guide

This project supports running PHPUnit tests with SQLite instead of MySQL/MariaDB, which is perfect for local development and CI/CD pipelines.

## Why SQLite?

- **Zero configuration**: No database server setup required
- **Fast**: In-memory or file-based database is typically faster for tests
- **Portable**: Database is just a file in `tmp/sqlite/test.sqlite`
- **CI/CD friendly**: No need to spin up MySQL containers in GitHub Actions
- **Clean slate**: Each test run can start fresh

## How It Works

The setup uses:
1. **aaemnnosttv/wp-sqlite-db**: A SQLite drop-in (`db.php`) that translates MySQL queries to SQLite
2. **bootstrap-sqlite.php**: Custom bootstrap that copies the SQLite drop-in into the WordPress test suite
3. **phpunit-sqlite.xml.dist**: PHPUnit configuration file that uses the SQLite bootstrap

## Quick Start

1. Install WordPress test suite (one-time):
   ```bash
   composer install-wp-tests
   ```

2. Run tests with SQLite:
   ```bash
   composer test:sqlite
   ```

That's it! No database configuration needed.

## Commands

```bash
# Run all tests with SQLite
composer test:sqlite

# Run specific test with SQLite
vendor/bin/phpunit -c phpunit-sqlite.xml.dist tests/example-test.php

# Run tests with coverage using SQLite
composer test:coverage:sqlite
```

## Database Location

The SQLite database file is stored at:
```
tmp/sqlite/test.sqlite
```

This directory is automatically created and is ignored by git.

## Limitations

While SQLite supports most WordPress functionality, some MySQL-specific features may not work:

- Complex JOIN operations with subqueries
- MySQL-specific functions (FIND_IN_SET, etc.)
- Some advanced full-text search features
- Certain MySQL spatial data types

For most WordPress VIP development, these limitations won't affect your tests. If you encounter issues, you can always fall back to MySQL tests using `composer test`.

## CI/CD Usage

SQLite is perfect for GitHub Actions and other CI/CD systems. Example workflow:

```yaml
- name: Install dependencies
  run: composer install

- name: Install WordPress test suite
  run: composer install-wp-tests

- name: Run tests
  run: composer test:sqlite
```

No MySQL service needed!

## Switching Between SQLite and MySQL

You can maintain both configurations:

- `composer test` - Use MySQL (requires DB-CONFIG)
- `composer test:sqlite` - Use SQLite (no configuration)

Choose based on your needs:
- **Local development**: Use SQLite for speed and convenience
- **Pre-production testing**: Use MySQL to match production environment

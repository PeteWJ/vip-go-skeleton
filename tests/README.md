# Tests

This directory contains PHPUnit tests for the WordPress VIP application.

## Setup

1. Install dependencies:
```bash
composer install
```

2. (Optional) Configure database connection by creating a `DB-CONFIG` file at the project root:
```txt
db_host: localhost
db_name: wordpress_tests
db_user: root
db_pass: ''
```

If you don't create this file, the defaults shown above will be used.

3. Install the WordPress test suite:
```bash
# Without database creation (use if DB already exists)
composer install-wp-tests

# With database creation
composer install-wp-tests-with-db
```

This installs the WordPress test suite in the `tmp/wordpress-tests-lib` directory.

## Running Tests

### With MySQL/MariaDB

Run all tests:
```bash
composer test
# or directly:
vendor/bin/phpunit
```

Run a specific test file:
```bash
vendor/bin/phpunit tests/example-test.php
```

Run tests with coverage:
```bash
composer test:coverage
```

### With SQLite (No Database Setup Required!)

SQLite is perfect for local development and CI/CD as it requires no database server setup.

Run all tests with SQLite:
```bash
composer test:sqlite
```

Run a specific test file with SQLite:
```bash
vendor/bin/phpunit -c phpunit-sqlite.xml.dist tests/example-test.php
```

Run tests with coverage using SQLite:
```bash
composer test:coverage:sqlite
```

**Note**: The SQLite database file is automatically created in `tmp/sqlite/test.sqlite`. Some advanced MySQL-specific features may not work with SQLite, but most WordPress core functionality is supported.

## Writing Tests

- Place test files in this directory with the suffix `-test.php`
- Extend `WP_UnitTestCase` for WordPress-specific tests
- Use Yoast PHPUnit Polyfills for cross-version compatibility
- Follow WordPress VIP coding standards

## Directory Structure

```
tests/
├── bootstrap.php          # PHPUnit bootstrap file
├── example-test.php       # Example test
└── README.md             # This file
```

## Resources

- [WordPress PHPUnit Documentation](https://make.wordpress.org/core/handbook/testing/automated-testing/phpunit/)
- [VIP Code Review Guidelines](https://docs.wpvip.com/development-workflow/code-review/)
- [Yoast PHPUnit Polyfills](https://github.com/Yoast/PHPUnit-Polyfills)
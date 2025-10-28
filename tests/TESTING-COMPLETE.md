# WordPress VIP PHPUnit Testing - Setup Complete!

## What Was Set Up

Your WordPress VIP skeleton now has a complete PHPUnit testing environment with **SQLite support**, which means:

✅ **Zero database configuration required**
✅ **Works in Codespaces/GitHub Actions out of the box**
✅ **No SVN dependency** (uses custom install script)
✅ **Fast test execution** with SQLite
✅ **Dual database support** (SQLite + MySQL)

## Quick Start

```bash
# 1. Install WordPress test suite (one-time)
composer install-wp-tests

# 2. Run tests with SQLite (no setup needed!)
composer test:sqlite

# Done! All tests passing ✓
```

## What Was Added

### Packages
- `phpunit/phpunit: ^9.5` - Test runner
- `yoast/phpunit-polyfills: ^1.0` - Cross-version compatibility
- `aaemnnosttv/wp-sqlite-db: ^1.3` - SQLite database support

### Scripts & Configuration
- `bin/install-wp-tests.sh` - Custom installer (no SVN needed!)
- `tests/bootstrap.php` - MySQL bootstrap
- `tests/bootstrap-sqlite.php` - SQLite bootstrap
- `phpunit.xml.dist` - MySQL configuration
- `phpunit-sqlite.xml.dist` - SQLite configuration
- `DB-CONFIG.example` - MySQL credentials template

### Documentation
- `tests/README.md` - Testing guide
- `tests/SQLITE.md` - SQLite-specific documentation
- `CLAUDE.md` - Updated with testing commands

### Example Tests
- `tests/example-test.php` - Sample tests demonstrating:
  - WordPress function availability
  - Database connectivity
  - Post creation with factory

## Commands Reference

```bash
# Installation
composer install-wp-tests          # Install WordPress test suite

# Running Tests
composer test:sqlite                # SQLite (recommended)
composer test                       # MySQL/MariaDB

# Coverage Reports
composer test:coverage:sqlite       # SQLite with coverage
composer test:coverage              # MySQL with coverage

# Run Specific Test
vendor/bin/phpunit -c phpunit-sqlite.xml.dist tests/example-test.php
```

## File Locations

- **Tests**: `tests/*.php` (files ending in `-test.php`)
- **Bootstrap**: `tests/bootstrap-sqlite.php`
- **WordPress Core**: `tmp/wordpress/`
- **Test Suite**: `tmp/wordpress-tests-lib/`
- **SQLite DB**: `tmp/sqlite/test.sqlite`
- **Configuration**: `phpunit-sqlite.xml.dist`

## Example Test

```php
<?php
class My_Feature_Test extends WP_UnitTestCase {

    public function test_my_feature() {
        // Create test data
        $post_id = $this->factory->post->create([
            'post_title' => 'Test',
        ]);

        // Run your code
        $result = my_custom_function( $post_id );

        // Assert expectations
        $this->assertTrue( $result );
    }
}
```

## CI/CD Integration

Perfect for GitHub Actions - no MySQL service required:

```yaml
- name: Install dependencies
  run: composer install

- name: Install WordPress test suite
  run: composer install-wp-tests

- name: Run tests
  run: composer test:sqlite
```

## Why SQLite?

1. **Zero Configuration**: No database server, no credentials
2. **Fast**: In-memory or file-based operations
3. **Portable**: Single file database
4. **CI-Friendly**: No container overhead
5. **Clean**: Fresh database per test run

## Troubleshooting

### Tests fail with "WordPress test suite not found"
Run: `composer install-wp-tests`

### SQLite drop-in not found
Run: `composer install`

### Want to use MySQL instead?
1. Create `DB-CONFIG` file with credentials
2. Run: `composer test`

## What's Next?

1. Write tests for your plugins/themes in `tests/`
2. Run tests locally with `composer test:sqlite`
3. Add testing to your CI/CD pipeline
4. Enjoy fast, reliable WordPress testing!

---

**Documentation**: See `tests/README.md` and `tests/SQLITE.md` for more details.

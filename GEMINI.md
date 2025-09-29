# GEMINI.md

## Project Overview

This is a Laravel package for creating distributed locks. The package is in its early stages of development, with many of the files serving as placeholders. The package uses `spatie/laravel-package-tools` for its structure and service provider.

**Key Technologies:**

*   PHP
*   Laravel

**Architecture:**

The project follows the standard structure for a Laravel package. The main logic is intended to be in the `src` directory, with a service provider to register the package with Laravel. It also includes a configuration file, a database migration, and an Artisan command.

## Building and Running

**Dependencies:**

*   PHP 8.4+
*   Composer

**Installation:**

1.  Install dependencies:
    ```bash
    composer install
    ```

**Running Tests:**

```bash
composer test
```

**Code Style:**

The project uses `laravel/pint` for code style. To format the code, run:

```bash
composer format
```

**Static Analysis:**

The project uses `larastan/larastan` for static analysis. To run the analysis, use:

```bash
composer analyse
```

## Development Conventions

*   **Namespace:** The main namespace for the package is `Pavons\Locky`.
*   **Service Provider:** The service provider is `Pavons\Locky\LockyServiceProvider`.
*   **Testing:** The project uses Pest for testing.
*   **Contributing:** Contribution guidelines are available in `CONTRIBUTING.md`.

# Contributing to Laravel Log Tracker

Thank you for considering a contribution! Please take a moment to read these guidelines.

## Requirements

- PHP 8.2+
- Composer

## Setup

```bash
git clone https://github.com/KsSadi/Laravel-Log-Tracker.git
cd Laravel-Log-Tracker
composer install
```

## Running Tests

```bash
vendor/bin/phpunit
```

All pull requests **must** pass the full test suite. New features and bug fixes should include tests covering both the happy path and failure cases.

## Code Style

This project uses [Laravel Pint](https://laravel.com/docs/pint) for code formatting. Run it before committing:

```bash
vendor/bin/pint
```

## Submitting a Pull Request

1. Fork the repository and create a branch from `main`.
2. Make your changes and add tests.
3. Run `vendor/bin/pint` to format your code.
4. Run `vendor/bin/phpunit` and confirm all tests pass.
5. Open a pull request with a clear description of the change and why it is needed.

## Reporting Issues

Please use [GitHub Issues](https://github.com/KsSadi/Laravel-Log-Tracker/issues) and include:
- Laravel and PHP versions
- Steps to reproduce
- Expected vs. actual behaviour

## License

By contributing, you agree that your contributions will be licensed under the [MIT License](LICENSE).

# Contributing

Contributions are welcome, and they are greatly appreciated! Every little bit helps, and credit will always be given.

## How Can I Contribute?

### Reporting Bugs

This section guides you through submitting a bug report for this project. Following these guidelines helps maintainers and the community understand your report, reproduce the behavior, and find related reports.

Before creating bug reports, please check the [issue tracker](https://github.com/pavons/laravel-locky/issues) as you might find out that you don't need to create one. When you are creating a bug report, please include as many details as possible. Fill out the required template, the information it asks for helps us resolve issues faster.

### Suggesting Enhancements

This section guides you through submitting an enhancement suggestion for this project, including completely new features and minor improvements to existing functionality. Following these guidelines helps maintainers and the community understand your suggestion and find related suggestions.

Before creating enhancement suggestions, please check the [issue tracker](https://github.com/pavons/laravel-locky/issues) as you might find out that you don't need to create one. When you are creating an enhancement suggestion, please include as many details as possible.

### Pull Requests

1.  Fork the repo and create your branch from `main`.
2.  Install dependencies: `composer install`
3.  Make your changes.
4.  Update the `README.md` with any relevant changes.
5.  Make sure your code lints: `composer format`
6.  Run the test suite: `composer test`
7.  Ensure the static analysis passes: `composer analyse`
8.  Issue that pull request!

## Commit Message Format

This project follows the [Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0-beta.4/) specification. All commit messages should be formatted as follows:

```
<type>[optional scope]: <description>

[optional body]

[optional footer(s)]
```

Please refer to the [Conventional Commits website](https://www.conventionalcommits.org/en/v1.0.0-beta.4/) for more information on the available types and formatting.

## Local Development

### Installation

```bash
composer install
```

### Running Tests

```bash
composer test
```

### Code Style

This project uses `laravel/pint` for code style. To format the code, run:

```bash
composer format
```

### Static Analysis

This project uses `larastan/larastan` for static analysis. To run the analysis, use:

```bash
composer analyse
```

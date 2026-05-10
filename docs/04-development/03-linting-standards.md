# Linting standards

Run:

```bash
composer lint
composer lint:all
composer lint:fix
```

Tool responsibilities:

- Pint formats Laravel-style PHP.
- PHPCS checks structural coding standard rules.
- PHPStan/Larastan performs static analysis.
- PHPMD catches maintainability issues in production code under `src/`.
- PHP-CS-Fixer performs narrow PHPDoc cleanup only; it is intentionally scoped
  so it does not compete with Pint.

`composer ci` is the canonical local full gate.

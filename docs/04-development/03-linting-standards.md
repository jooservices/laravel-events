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
- PHPMD catches maintainability issues.

`composer ci` is the canonical local full gate.

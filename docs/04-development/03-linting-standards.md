# Linting standards

Run:

```bash
composer lint
composer lint:fix
```

Tool responsibilities:

- Pint formats PHP with the `per` preset (PER-CS 3.0).
- PHPCS checks structural coding standard rules (Pint wins on style conflicts).
- PHPStan/Larastan performs static analysis.
- PHPMD catches maintainability issues under `src/` and `tests/`.
- PHP-CS-Fixer performs narrow PHPDoc cleanup only; it is intentionally scoped
  so it does not compete with Pint.

`composer lint` already includes Pint, PHPCS, PHPStan, PHPMD, and PHP-CS-Fixer.
`composer ci` is the canonical local full gate (`lint` + coverage test).

# Testing

Run:

```bash
composer test
composer test:coverage
```

Use unit tests for pure helpers and value objects. Use integration tests for
event sourcing persistence, audit log persistence, index installation, config
publishing, and query behavior.

Do not fake persisted event or audit data when the behavior under test depends
on MongoDB storage.

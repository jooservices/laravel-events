# Index installation

Run the package command after MongoDB is configured:

```bash
php artisan events:install-indexes
```

The command creates recommended indexes for aggregate history, entity audit
history, action lookups, user lookups, and timestamp ordering.

To drop package-managed indexes without deleting data:

```bash
php artisan events:install-indexes --drop --force
```

Use TTL retention only after confirming that automatic asynchronous deletion is
acceptable for the collection.

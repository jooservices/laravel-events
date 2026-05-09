# Publish assets

The package publishes a single configuration file:

```bash
php artisan vendor:publish --tag=laravel-events-config
```

This creates `config/events.php` in the application.

No migrations are published because this package stores records in MongoDB
collections. Use [index installation](05-index-installation.md) to create the
recommended MongoDB indexes.

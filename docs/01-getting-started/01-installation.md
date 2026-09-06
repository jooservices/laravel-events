# Installation

## Requirements

- **PHP** ^8.5
- **Laravel** ^12.0|^13.0
- **MongoDB** (server) and PHP MongoDB extension
- **mongodb/laravel-mongodb** ^5.7 (MongoDB Eloquent adapter for Laravel)

## Composer

```bash
composer require jooservices/laravel-events:^4.0
```

Upgrading from `1.x`? Follow [`UPGRADE-4.0.md`](../../UPGRADE-4.0.md).

## Laravel Setup

1. **Publish config (optional)**  
   To customize connection, collection names, or TTL:

   ```bash
   php artisan vendor:publish --tag=laravel-events-config
   ```

   This creates `config/events.php`. Edit as needed.

2. **MongoDB connection**  
   Ensure a `mongodb` connection exists in `config/database.php`. See [mongodb/laravel-mongodb](https://github.com/mongodb/laravel-mongodb) for setup.

3. **Environment variables** (optional)

   ```env
   MONGODB_URI=mongodb://127.0.0.1:27017
   MONGODB_DATABASE=your_db
   EVENTS_EVENTSOURCING_ENABLED=true
   EVENTS_EVENT_LOG_ENABLED=true
   EVENTS_STORED_EVENTS_COLLECTION=stored_events
   EVENTS_EVENT_LOGS_COLLECTION=event_logs
   EVENTS_EVENTSOURCING_TTL_DAYS=
   EVENTS_EVENT_LOG_TTL_DAYS=
   ```

## Indexes and TTL

Create recommended indexes (and optional TTL) for both collections:

```bash
php artisan events:install-indexes
```

- **stored_events:** `aggregate_id + created_at`, `event_class + created_at`, `event_name + created_at`, sparse unique `event_id`, `event_category`, correlation/causation (metadata + top-level), `user_id`, `created_at` (plus optional TTL)
- **event_logs:** `(entity_type, entity_id, created_at)`, `action + created_at`, `meta.correlation_id` / `causation_id`, `user_id`, `created_at` (plus optional TTL)

To drop indexes (data is not deleted):

```bash
php artisan events:install-indexes --drop [--force]
```

See [Configuration](02-configuration.md) for TTL and context provider.

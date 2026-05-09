# Laravel Events Development

Use this skill for runtime changes in `jooservices/laravel-events`.

Package architecture:

- Laravel native event dispatching remains the integration point
- `EventSourcingInterface` records are persisted to `stored_events`
- `LoggableModelInterface` records are persisted to `event_logs`
- `EventService` owns persistence orchestration
- MongoDB connection names, collection names, retention, redaction, and context behavior stay configurable through `config/events.php`

Compatibility rules:

- do not rename public interfaces/classes without explicit approval
- do not break existing config keys or existing MongoDB field names
- additive nullable fields are allowed only when documented and tested
- keep Event Sourcing and Event Log concepts separate
- do not add projections, dashboards, authorization, tenant filtering, replay side effects, outbox runtime, or AI runtime unless explicitly requested

Validation:

- use real MongoDB integration tests when storage behavior changes
- report skipped MongoDB tests exactly when MongoDB is unavailable
- run `composer validate --strict`, `composer lint:all`, `composer test`, and `composer check` before claiming completion
- use Laravel 12 / PHP 8.5 standards, let Pint win formatter conflicts, and update docs when public behavior changes

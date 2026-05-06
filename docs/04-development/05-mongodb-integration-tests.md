# MongoDB integration tests

Persistence tests require a real MongoDB connection.

Typical local environment:

```env
MONGODB_URI=mongodb://127.0.0.1:27017
MONGODB_DATABASE=laravel_events_test
```

CI provides a MongoDB service for test coverage. Integration tests should clean
their collections between runs and avoid relying on pre-existing data.

# Optional AI Integration

This package is not an AI package. It does not fetch event data for AI tools, perform authorization, redact payloads, create agents, or provide an AI query runtime.

If an application wants an AI system to interpret event history, build that integration in the application layer.

## Application Responsibilities

The application must handle:

- authorization and tenant boundaries
- selecting which records can be exported
- redacting secrets and PII
- limiting payload size and time ranges
- explaining event semantics to the external system
- logging access to exported audit data

## Sample Event Manifest

```json
{
  "events": [
    {
      "name": "OrderPlaced",
      "class": "App\\Events\\OrderPlaced",
      "aggregate": "order",
      "schema_version": 1,
      "description": "A customer placed an order.",
      "payload_keys": ["order_id", "customer_id", "total", "currency"]
    }
  ],
  "event_log_actions": [
    "created",
    "updated",
    "deleted",
    "restored",
    "status_changed",
    "corrected"
  ]
}
```

## Sample Sanitized Export

```json
{
  "aggregate_id": "ORD-123",
  "records": [
    {
      "type": "stored_event",
      "event_class": "App\\Events\\OrderPlaced",
      "payload": {
        "order_id": "ORD-123",
        "total": 149.95,
        "currency": "USD"
      },
      "metadata": {
        "schema_version": 1,
        "correlation_id": "checkout-456",
        "channel": "api"
      },
      "occurred_at": "2026-04-21T08:00:00Z"
    }
  ]
}
```

## Sample External Tool Schema

```json
{
  "name": "get_event_history",
  "description": "Return sanitized event history for an authorized aggregate.",
  "input_schema": {
    "type": "object",
    "properties": {
      "aggregate_id": { "type": "string" },
      "from": { "type": "string", "format": "date-time" },
      "to": { "type": "string", "format": "date-time" }
    },
    "required": ["aggregate_id"]
  }
}
```

Keep this as an application-owned integration. The package should remain a lightweight persistence library.

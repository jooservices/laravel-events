# Architecture

## Overview

Laravel Events integrates with Laravel's event system. When you dispatch events that implement the package interfaces, **subscribers** listen and persist them to MongoDB via the **EventService**. Two persistence modes are supported: **EventSourcing** (event payloads by aggregate) and **EventLog** (model change audit with prev/changed/diff).

The package boundary is intentionally small: it persists event records and log entries. It does not replace Laravel's dispatcher, run projections/read models, provide analytics/reporting, or provide AI data access.

## High-Level Architecture

```mermaid
flowchart TB
    subgraph App["Laravel Application"]
        A[Domain / App Code]
        A -->|dispatch| B[Event Dispatcher]
    end

    subgraph Package["JOOservices\\LaravelEvents"]
        B -->|EventSourcingInterface| C[EventSourcingSubscriber]
        B -->|LoggableModelInterface| D[EventLogSubscriber]
        C --> E[EventService]
        D --> E
        E --> F[(MongoDB)]
    end

    F --> G[stored_events]
    F --> H[event_logs]
```

## Component Diagram

```mermaid
flowchart LR
    subgraph Contracts
        ESI[EventSourcingInterface]
        LMI[LoggableModelInterface]
        HLA[HasLogAction]
    end

    subgraph Subscribers
        ESS[EventSourcingSubscriber]
        ELS[EventLogSubscriber]
    end

    subgraph Core
        SVC[EventService]
        DH[DiffHelper]
    end

    subgraph Persistence
        SE[StoredEvent Model]
        EL[EventLogEntry Model]
    end

    ESI --> ESS
    LMI --> ELS
    HLA --> ELS
    ESS --> SVC
    ELS --> SVC
    ELS --> DH
    SVC --> SE
    SVC --> EL
```

## Data Flow: Event Sourcing

```mermaid
sequenceDiagram
    participant App
    participant Dispatcher
    participant Subscriber
    participant EventService
    participant MongoDB

    App->>Dispatcher: dispatch(OrderCreated)
    Dispatcher->>Subscriber: persistEvent(OrderCreated)
    Subscriber->>Subscriber: occurredAt?, metadata?
    Subscriber->>EventService: storeEvent(event, payload, aggregateId, ...)
    EventService->>EventService: merge context_provider + metadata
    EventService->>MongoDB: stored_events.insertOne(...)
```

## Data Flow: Event Log (Audit)

```mermaid
sequenceDiagram
    participant App
    participant Dispatcher
    participant Subscriber
    participant DiffHelper
    participant EventService
    participant MongoDB

    App->>Dispatcher: dispatch(OrderUpdated event)
    Dispatcher->>Subscriber: logModelChange(event)
    Subscriber->>Subscriber: getPrev(), getChanged()
    Subscriber->>DiffHelper: diff(prev, current)
    Subscriber->>EventService: logChange(entityType, entityId, action, prev, changed, diff, meta)
    EventService->>MongoDB: event_logs.insertOne(...)
```

## MongoDB Collections

| Collection | Purpose | Key Fields |
|------------|---------|------------|
| **stored_events** | Event Sourcing: event payloads by aggregate | `event_class`, `aggregate_id`, `payload`, `metadata`, `user_id`, `occurred_at`, `created_at` |
| **event_logs** | Event Log: model change audit | `entity_type`, `entity_id`, `action`, `prev`, `changed`, `diff`, `meta`, `user_id`, `created_at` |

## Design Decisions

- **Laravel events:** Uses native `Dispatcher`; no custom bus. Enables middleware, queuing, and testing with Laravel's tools.
- **MongoDB:** Chosen for flexible schema, scalability, and TTL support for retention policies.
- **Single EventService:** Both features use one service and one MongoDB connection for consistency and configuration.
- **Context provider:** Optional callable injects request-scoped metadata (e.g. `request_id`, `channel`) into every stored record.
- **Conventions over enforcement:** Metadata, versioning, correction links, and action taxonomy are documented conventions with small helpers, not rigid runtime validation.

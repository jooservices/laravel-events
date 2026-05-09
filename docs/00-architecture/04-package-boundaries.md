# Package boundaries

This package provides Laravel-native persistence for:

- domain event records in MongoDB
- audit event log records in MongoDB
- recommended MongoDB indexes for those records
- small metadata and support helpers around those persistence flows

This package does not provide:

- dashboards or reporting UI
- business analytics
- projections or read-model framework
- event replay orchestration
- AI runtime, AI agents, or AI data fetching
- application authorization or tenant policy
- domain-specific event catalogs

Applications remain responsible for deciding which events to persist, how long
records are retained, how historical events are interpreted, and who can read
audit data.

# Bulk record example

Bulk record APIs are planned for this pass only if they fit the current
architecture cleanly.

Until then, dispatch events normally or call `EventService` one record at a
time. Applications that need high-volume batch ingestion should measure MongoDB
write behavior and avoid bypassing package validation or normalization.

# PHP Package Development

Use PHP 8.5 and Laravel 12 package conventions.

Keep public APIs small, typed, and backward compatible where practical. Convert
unstructured arrays to typed data near service boundaries and persist MongoDB
arrays only at the storage boundary.

Required behavior:

- inspect the current code, docs, tests, and Composer metadata before changing public APIs
- keep Pint as the formatting authority
- use real MongoDB persistence tests for storage behavior
- update docs when public behavior changes
- stop and ask when requirements or compatibility are unclear
- run the relevant Composer quality gates before done

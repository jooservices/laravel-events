# Docs sync policy

The documentation tree follows the current DTO-style reading order:

```text
docs/
  00-architecture/
  01-getting-started/
  02-user-guide/
  03-examples/
  04-development/
  05-maintenance/
```

Use `docs/05-maintenance` for repository risks, docs changelog entries,
audits, and sync policy notes. Do not create `docs/05-ai-contributor-guide`;
AI contributor guidance belongs in `AGENTS.md`, tool prompts, or development
docs when it is about contributor workflow rather than package runtime.

Sync docs when public behavior, Composer commands, CI gates, branch policy,
configuration, or package boundaries change. Run the relevant focused checks and
`composer ci` before treating a docs-affecting implementation change as ready.

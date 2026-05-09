# Prompt sync policy

Prompt files for `.claude/commands`, `.github/prompts`, `antigravity/prompts`,
and `jetbrains/prompts` are tool-specific wrappers around the same repository
rules. Keep their wording short, but keep command names, branch policy, docs
locations, and package boundaries synchronized.

When a prompt changes, check the matching files in the other tool directories
and update this maintenance policy if the expected behavior changes. Release
readiness prompts must require a clean git state, synced branches, updated docs
or changelog when relevant, safe dependency state, and the canonical local gate:
`composer ci`.

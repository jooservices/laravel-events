# Git Workflow

Use this skill before starting implementation work.

Required behavior:

- inspect `git status`, current branch, remotes, recent log, and open pull requests when possible
- normal work starts from the latest `develop`
- normal pull requests target `develop`
- `master` is production/release state
- hotfix work starts from `master` and targets `master`
- sync hotfixes back into `develop`
- never commit directly to `master` or `develop` unless explicitly approved
- stop and ask if branch state is unclear, dirty, conflicting, or not safe for the requested task

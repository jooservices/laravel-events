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
- before handoff, verify relevant Composer quality gates passed and the worktree is clean
- use the repository's Laravel 12 / PHP 8.5 docs and real MongoDB test requirements when judging whether a feature branch is ready
- remember Pint is the formatting authority when reviewing whether branch changes are ready

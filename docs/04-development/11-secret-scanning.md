# Secret scanning

Local hooks run staged secret scanning with `gitleaks` when available. Install it
locally with Homebrew on macOS:

```bash
brew install gitleaks
```

For Linux, Windows, and Docker options, use the official
[`gitleaks` installation guide](https://github.com/zricethezav/gitleaks#installation).
Common alternatives include downloading the Linux release binary, using
Chocolatey or Scoop on Windows, or running the published Docker image.

Never commit tokens, passwords, private keys, cookies, authorization headers, or
real customer data in tests, docs, fixtures, or examples.

GitHub Actions also runs `.github/workflows/secret-scanning.yml` with Gitleaks on:

- pushes to `master` and `develop`
- pull requests targeting `master` or `develop`
- manual workflow dispatch

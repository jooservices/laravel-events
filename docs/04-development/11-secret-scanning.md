# Secret scanning

Local hooks run staged secret scanning with `gitleaks` when available. Install it
locally with:

```bash
brew install gitleaks
```

Never commit tokens, passwords, private keys, cookies, authorization headers, or
real customer data in tests, docs, fixtures, or examples.

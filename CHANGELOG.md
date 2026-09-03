# Changelog

## 2.0.0

- Use the canonical `https://api.kobara.app/v1` base URL.
- Support all Production payment provider selectors.
- Align MonCash and NatCash withdrawals with Production.
- Generate idempotency keys automatically.
- Verify timestamped webhook signatures with replay protection.
- Fix Composer PSR-4 exception autoloading.
- Remove public v1 methods that did not exist and returned 404.

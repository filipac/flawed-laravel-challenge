# Account settlement review

You have joined a billing team whose `POST /api/accounts/{account}/settle` endpoint settles every pending invoice for an account. A second endpoint, `GET /api/accounts/{account}/summary`, serves a cached account summary.

Start with `app/Actions/SettleAccountInvoices.php`, then follow any code paths that affect your review.

## Business contract

A settlement request contains:

- `fee_percent`: required numeric percentage from `0` to `5`.
- `processed_by`: required non-empty operator name, at most 100 characters.

For every pending invoice, the endpoint must record the unpaid amount and fee, mark the invoice settled, debit the account, and queue one receipt. A failed settlement must not leave part of the account processed. The JSON response must retain its current shape. The summary endpoint may be cached for ten minutes, but it must not show pre-settlement data after a successful settlement.

## Your task

Review the implementation before rewriting it. Explain:

1. What can go wrong, ranked by production impact.
2. Which observations are correctness issues versus performance issues.
3. What you would test to prove each claim.
4. The smallest safe change you would ship first.

If the interviewer asks you to implement a fix, keep the response contract and existing happy-path tests intact. Add focused tests for the behavior you change.

## Running the project

```bash
php artisan migrate:fresh --seed
php artisan test --compact tests/Feature/SettleAccountInvoicesTest.php tests/Feature/AccountSummaryTest.php
```

The seeded account ID is `1`:

```bash
curl http://localhost:8000/api/accounts/1/summary
curl -X POST http://localhost:8000/api/accounts/1/settle \
  -H 'Content-Type: application/json' \
  -d '{"fee_percent":2.5,"processed_by":"Jordan Lee"}'
```

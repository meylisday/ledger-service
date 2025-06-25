## Solution Overview

This project implements a basic multi-currency ledger service using Symfony 7, PHP 8.3, and PostgreSQL. The service allows creation of ledgers, recording of transactions in various currencies, real-time balance reporting, and simulated currency conversion.

---

## Technical Architecture

* **Framework:** Symfony 7 with attribute-based routing
* **Language:** PHP 8.3
* **Database:** PostgreSQL 15, ACID-compliant transactional integrity
* **ORM:** Doctrine with UUID support for entity IDs
* **Containerization:** Docker and Docker Compose setup
* **Currency Conversion:** Local MockCurrencyConverter with hardcoded exchange rates
* **Rate Limiting:** Symfony RateLimiter (100 requests/minute, IP-based)
* **Error Handling:** Global ExceptionListener for unified JSON error responses
* **API Documentation:** OpenAPI/Swagger with NelmioApiDocBundle

---

## Multi-Currency Support

* Ledgers store a base currency (e.g., USD)
* Transactions can be recorded in any supported currency (USD, EUR)
* Balances are calculated per currency
* Conversion endpoint calculates total balance in target currency on the fly

**MockCurrencyConverter:** Simulates external exchange rates for demo/testing

---

## API Usage Highlights

| Endpoint                       | Method | Description                                    |
| ------------------------------ | ------ | ---------------------------------------------- |
| `/ledgers`                     | POST   | Create a new ledger                            |
| `/transactions`                | POST   | Record a transaction in any supported currency |
| `/balances/{ledgerId}`         | GET    | Get per-currency balances of a ledger          |
| `/balances/{ledgerId}/convert` | GET    | Convert total balance to target currency       |

Swagger UI available at:

```
http://localhost:8080/api/doc
```

---

## Testing Notes (for QA)

* Verify ledger creation with different base currencies
* Record transactions with both matching and different currencies
* Validate per-currency balance reporting
* Test conversion endpoint with supported currencies (USD ↔ EUR)
* Exceed 100 requests/minute to confirm RateLimiter blocks requests (returns 429)
* Ensure proper error messages for invalid inputs (missing fields, unsupported currencies)

---

## Limitations & Future Enhancements

* MockCurrencyConverter uses fixed, hardcoded rates for simplicity
* No real-time external API integration yet
* No authentication or user management implemented
* Limited currency list (currently USD, EUR)
* No persistent currency exchange rate storage

**Suggested Improvements:**

* Integrate real currency API (e.g., exchangeratesapi.io)
* Expand currency support beyond USD and EUR
* Add authentication/authorization layer
* Implement request tracing, logging, and monitoring for production readiness

---

## Development Utilities

* Local development via Docker:

```bash
docker-compose up --build
```

* Database migrations:

```bash
docker-compose exec app php bin/console doctrine:migrations:migrate
```

* Run test suite:

```bash
docker-compose exec app php bin/phpunit
```

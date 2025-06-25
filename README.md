# Multi-Currency Ledger Service

A basic yet robust multi-currency ledger service built with Symfony 7 and PHP 8.3. Supports real-time ledger management, multi-currency transactions, and balance reporting.

---

## Setup Instructions

### Prerequisites

* Docker & Docker Compose
* PHP 8.3 (if running without Docker)
* Composer

### Local Development (Recommended with Docker)

```bash
docker-compose up --build
```

Symfony will be available at: [http://localhost:8080](http://localhost:8080)

Database: PostgreSQL running inside Docker (`ledger` database)

### Database Setup

```bash
docker-compose exec app php bin/console doctrine:migrations:migrate
```

---

## API Endpoints

All endpoints accept and return JSON.

### 1. Create a Ledger

```http
POST /ledgers
```

**Body:**

```json
{
    "currency": "USD"
}
```

---

### 2. Record a Transaction

```http
POST /transactions
```

**Body:**

```json
{
    "ledgerId": "UUID",
    "transactionId": "Unique Transaction UUID",
    "type": "credit | debit",
    "amount": "100.00",
    "currency": "USD"
}
```

---

### 3. Get Ledger Balances

```http
GET /balances/{ledgerId}
```

**Response Example:**

```json
{
    "USD": "150.00",
    "EUR": "50.00"
}
```

---

### 4. Convert Total Balance to Target Currency

```http
GET /balances/{ledgerId}/convert?currency=USD
```

---

## Architecture Overview

* Symfony 7 with Attributes and PHP 8.3 features
* Dockerized setup with Nginx and PostgreSQL
* Multi-currency support with on-the-fly conversion using a MockCurrencyConverter
* Rate limiting implemented to prevent abuse (100 requests/minute)
* OpenAPI/Swagger integration for easy API testing
* Clean error handling with centralized ExceptionListener
* Unit and integration tests with PHPUnit

---

## Running Tests

```bash
docker-compose exec app php bin/phpunit
```

---

## Future Improvements

* Replace MockCurrencyConverter with real-time external currency API
* Add authentication & authorization layers
* Implement real production-grade logging and monitoring

---

## Swagger Documentation

After project setup, access API documentation at:

```
http://localhost:8080/api/doc
```

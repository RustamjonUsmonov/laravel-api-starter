# Laravel API Starter

![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)
![PHP](https://img.shields.io/badge/PHP-8.2-blue.svg)
![Tests](https://img.shields.io/badge/Tests-Passing-brightgreen.svg)

Welcome to the **Laravel API Starter**, a modern, scalable, and feature-rich API boilerplate built with **Laravel 12** and **PHP 8.2**. This project is designed for developers who want to kickstart a robust API with **CQRS**, **Domain-Driven Design (DDD)**, **Data Transfer Objects (DTOs)**, **Spatie Permissions**, **Laravel Sanctum**, **Swagger documentation**, and comprehensive **feature tests**. Whether you’re building a SaaS, microservice, or enterprise API, this starter has you covered.

## ✨ Features

- **Modern Architecture**:
    - **CQRS** (Command Query Responsibility Segregation) for clean separation of reads and writes.
    - **DDD** (Domain-Driven Design) for a structured, maintainable codebase.
    - **DTOs** (Data Transfer Objects) for type-safe, validated data transfer.

- **Authentication & Authorization**:
    - **Laravel Sanctum** for secure API token authentication.
    - **Spatie Laravel Permission** for role-based access control (RBAC).
    - Routes for registration, login, logout, password management, and email verification.

- **API Documentation**:
    - **Swagger** (OpenAPI) integration for interactive, auto-generated API docs.

- **Testing**:
    - Comprehensive **feature tests** using **Pest** to ensure reliability.
    - Middleware for logging commands/queries with sensitive data masking.

- **Scalability & Security**:
    - Middleware for token expiration and signed URLs.
    - Sensitive data (e.g., `email`, `api_token`, `hash`) masked in logs.
    - Built with **Laravel 12** and **PHP 8.2** for modern performance and type safety.

## 🚀 Getting Started

### Prerequisites

- **PHP**: 8.2 or higher
- **Composer**: 2.x
- **Database**: MySQL
- **Redis** (optional): For queue or caching
- **Swagger UI**: For API documentation (configured via Laravel package)

### Installation

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/RustamjonUsmonov/laravel-api-starter.git
   cd laravel-api-starter
   ```

2. **Install Dependencies**:
   ```bash
   composer install
   ```

3. **Set Up Environment**:
    - Copy the `.env.example` to `.env`:
      ```bash
      cp .env.example .env
      ```
    - Configure your `.env` file with database, Sanctum, and other settings:
      ```env
      APP_NAME="Laravel API Starter"
      APP_URL=http://localhost
      DB_CONNECTION=mysql
      DB_HOST=127.0.0.1
      DB_PORT=3306
      DB_DATABASE=laravel_api
      DB_USERNAME=root
      DB_PASSWORD=
      ```

4. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

5. **Run Migrations & Seeders**:
   ```bash
   php artisan migrate --seed
   ```

6. **Start the Development Server**:
   ```bash
   php artisan serve
   ```
   Access the API at `http://localhost:8000`.

7. **View Swagger Docs**:
    - Run:
      ```bash
      php artisan l5-swagger:generate
      ```
    - Access at `http://localhost:8000/api/documentation`.

### Configuration

- **Sanctum**: Ensure `SANCTUM_STATEFUL_DOMAINS` is set for SPA or cross-origin requests.
- **Spatie Permissions**: Roles and permissions are seeded. Customize in `database/seeders/RolePermissionSeeder.php`.
- **Logging**: Sensitive fields (`password`, `access_token`, `api_token`, `hash`, `email`) are masked in logs. Update in `config/logging.php`:
  ```php
  'sensitive_fields' => [
      'password',
      'access_token',
      'api_token',
      'hash',
      'email',
  ]
  ```
- **Swagger**: Configure in `config/l5-swagger.php` for custom API documentation settings.

## 📜 API Endpoints

The API provides endpoints for authentication, password management, token handling, and email verification, protected by **Sanctum** and **signed URLs**.

| Method | Endpoint                           | Action                          | Middleware                     | Description                              |
|--------|------------------------------------|---------------------------------|-------------------------------|------------------------------------------|
| POST   | `/api/v1/register`                 | `AuthController@register`       | None                          | Register a new user                      |
| POST   | `/api/v1/login`                    | `AuthController@login`          | None                          | Log in and receive an API token          |
| POST   | `/api/v1/forgot-password`          | `AuthController@forget`         | None                          | Request a password reset link            |
| POST   | `/api/v1/reset-password`           | `AuthController@reset`          | None                          | Reset password using a token             |
| POST   | `/api/v1/update-password`          | `AuthController@updatePassword` | `auth:sanctum,token.expires`  | Update password for authenticated user   |
| POST   | `/api/v1/refresh`                  | `AuthController@refresh`        | `refresh.sanctum`             | Refresh API token                        |
| POST   | `/api/v1/logout`                   | `AuthController@logout`         | `auth:sanctum,token.expires`  | Log out and revoke token                 |
| GET    | `/api/v1/verify-email/{id}/{hash}` | `AuthController@verifyEmail`    | `signed`                      | Verify user’s email address              |


## 🛠️ Project Structure

The project follows **DDD** principles with a modular structure:

```
app/
├── Domains/                    # DDD domains (e.g., Authorization)
│   ├── Authorization/
│   │   ├── Commands/          # CQRS commands (e.g., RegisterUserCommand)
│   │   ├── Queries/           # CQRS queries
│   │   ├── DTO/               # Data Transfer Objects (e.g., VerifyEmailDTO)
│   │   └── ...
├── Http/
│   ├── Controllers/           # API controllers (e.g., AuthController)
│   ├── Middleware/            # Custom middleware (e.g., LoggingMiddleware)
│   └── ...
config/                        # Configuration files (e.g., logging.php)
database/                      # Migrations, seeders
tests/                         # Pest feature and unit tests
```

## 🧪 Testing

The project uses **Pest** for testing, with **feature tests** covering authentication, authorization, and CQRS pipelines.
By default, it uses mysql, so you have to create a test database `test_db`.

Run tests:
```bash
./vendor/bin/pest
```

## 🛠️ Code Quality with Rector

This project leverages **Rector**, a powerful PHP code refactoring tool, to ensure high-quality, modern, and maintainable code. Rector automates code upgrades, enforces best practices, and keeps the codebase aligned with **PHP 8.2** and **Laravel 12** standards. It’s integrated into the development workflow to streamline refactoring and improve code consistency.

### Rector Configuration

The project uses a custom `rector.php` configuration to apply specific rules and sets tailored to the Laravel API starter:

- **Paths**: Rector scans key directories (`app`, `bootstrap`, `config`, `public`, `resources`, `routes`, `tests`), excluding `bootstrap/cache` to avoid processing cached files.
- **Rules**:
    - `DeclareStrictTypesRector`: Adds `declare(strict_types=1);` to all PHP files, ensuring type safety.
    - `ValidationRuleArrayStringValueToArrayRector`: Converts Laravel validation rule strings (e.g., `'required|file'`) to array format (e.g., `['required', 'file']`) for better readability and consistency.
- **Sets**:
    - `CODE_QUALITY`: Improves readability and enforces coding standards.
    - `DEAD_CODE`: Removes unused code to keep the codebase lean.
    - `TYPE_DECLARATION`: Adds type hints and return types for better type safety.
    - `EARLY_RETURN`: Simplifies control structures by promoting early returns.
    - `PRIVATIZATION`: Enforces encapsulation by making properties and methods private where possible.
- **PHP 8.3 Features**: Applies PHP 8.3-specific upgrades to leverage the latest language features.
- **Import Names**: Removes unused imports and preserves fully qualified class names for clarity.

### Usage

Run Rector to analyze and refactor the codebase:
```bash
composer rector #vendor/bin/rector
```

Rector is a key part of the project’s commitment to delivering a robust and future-proof Laravel API.

## 📝 Logging

The `LoggingMiddleware` ensures secure logging of CQRS commands and queries:
- Masks sensitive fields (`password`, `access_token`, `api_token`, `hash`, `email`) in logs.
- Supports nested DTOs (e.g., `verifyEmailDTO`).
- Logs to the `daily` channel (configurable in `config/logging.php`).

Example log:
```
[2025-05-06 09:15:09] daily.INFO: Dispatched: App\Domains\Authorization\Commands\VerifyEmailCommand {"result":{"id":1,"name":"John Doe","email":"****","email_verified_at":"2025-05-06T09:15:09.000000Z"}}
```

## 🔐 Security

- **Sanctum**: Token-based authentication with expiration middleware (`token.expires`).
- **Spatie Permissions**: Role-based access control for fine-grained authorization.
- **Signed URLs**: Email verification routes are protected with signed URLs (`signed` middleware).
- **Sensitive Data**: Logging middleware masks PII and tokens to comply with GDPR and security best practices.

## 🤝 Contributing

Contributions are welcome! Please follow these steps:
1. Fork the repository.
2. Create a feature branch (`git checkout -b feature/awesome-feature`).
3. Commit changes (`git commit -m 'Add awesome feature'`).
4. Push to the branch (`git push origin feature/awesome-feature`).
5. Open a Pull Request.


## 🙌 Acknowledgments

- [Laravel](https://laravel.com)
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger)
- [Pest](https://pestphp.com)

---

Built with 💙 by RJUSM. Star the repo if you find it useful! 🌟

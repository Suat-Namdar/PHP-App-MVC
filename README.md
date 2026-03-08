# PHP-App-MVC

PHP ile yazılan MVC RBAC özellikli Web uygulaması.  
A lightweight **MVC web application** with full **Role-Based Access Control (RBAC)** written in pure PHP 8.

---

## Features

- 🏗 Custom **MVC framework** (Router, Controllers, Models, Views)
- 🔐 **Authentication** – login / logout with session management and CSRF protection
- 🛡 **RBAC** – Roles, Permissions, and fine-grained middleware guards
- 👤 **User management** – create, edit, delete users and assign roles
- 🏷 **Role management** – create, edit, delete roles and assign permissions
- 🗄 **SQLite database** (zero config) or MySQL/MariaDB
- ✅ **PHPUnit test suite** – 44 tests covering Models, RBAC, Router, and Session
- 💅 Bootstrap 5 responsive UI

---

## Project Structure

```
PHP-App-MVC/
├── app/
│   ├── Controllers/       # AuthController, DashboardController, UserController, RoleController
│   ├── Core/              # Application, Router, Database (PDO), Session, Controller
│   ├── Helpers/           # Global helper functions (e, can, has_role, csrf_field …)
│   ├── Middleware/        # AuthMiddleware + RBAC permission middlewares
│   ├── Models/            # User, Role, Permission
│   └── Views/             # PHP templates (Bootstrap 5)
├── config/
│   └── app.php            # Application bootstrap & route definitions
├── database/
│   ├── schema.sql         # Database schema (SQLite / MySQL compatible)
│   └── seed.php           # Creates tables + default users/roles/permissions
├── public/
│   ├── index.php          # Front controller
│   └── .htaccess          # URL rewriting
├── tests/
│   ├── Unit/              # Model, Router, Session, RBAC tests
│   └── bootstrap.php      # In-memory SQLite for testing
├── .env.example
├── composer.json
└── phpunit.xml
```

---

## Requirements

- PHP 8.0+
- Composer
- SQLite3 extension (default) **or** MySQL 5.7+ / MariaDB 10.3+
- Apache / Nginx with `mod_rewrite`

---

## Quick Start

```bash
# 1. Clone and install dependencies
git clone https://github.com/Suat-Namdar/PHP-App-MVC.git
cd PHP-App-MVC
composer install

# 2. Configure environment
cp .env.example .env

# 3. Create database & seed default data
php database/seed.php

# 4. Start the built-in PHP server (development)
php -S localhost:8000 -t public/
```

Then open **http://localhost:8000** in your browser.

### Default credentials

| Username | Password  | Role   |
|----------|-----------|--------|
| admin    | admin123  | admin  |
| editor   | editor123 | editor |
| viewer   | viewer123 | viewer |

---

## RBAC Overview

### Permissions

| Permission           | Description                |
|----------------------|----------------------------|
| `user.view`          | List users                 |
| `user.create`        | Create new users           |
| `user.edit`          | Edit existing users        |
| `user.delete`        | Delete users               |
| `role.manage`        | Create / edit / delete roles |
| `permission.manage`  | Manage permissions         |

### Default Roles

| Role    | Permissions                                          |
|---------|------------------------------------------------------|
| admin   | All permissions                                      |
| editor  | `user.view`, `user.edit`                             |
| viewer  | `user.view`                                          |

---

## Running Tests

```bash
php vendor/bin/phpunit
```

All tests use an **in-memory SQLite** database – no configuration needed.

---

## Using MySQL Instead of SQLite

Edit `.env`:

```dotenv
DB_DRIVER=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=php_mvc
DB_USERNAME=root
DB_PASSWORD=secret
```

Then run:

```bash
php database/seed.php
```


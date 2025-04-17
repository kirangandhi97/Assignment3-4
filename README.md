<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <a href="https://github.com/kirangandhi97/TradeFinanceSystem/actions"><img src="https://github.com/kirangandhi97/TradeFinanceSystem/workflows/tests/badge.svg" alt="Build Status"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# TradeFinanceSystem

A comprehensive Laravel-based web application to manage the complete lifecycle of financial guarantees, including creation, review, issuance, and bulk processing using CSV, JSON, or XML.

---

## 🚀 Overview

**TradeFinanceSystem** is a secure, role-based platform designed for financial institutions to efficiently handle trade finance guarantees. It supports manual creation and bulk uploads, with a multi-stage workflow including review, approval, and issuance.

---

## ✨ Features

- **User Authentication**
  - Role-based access (Admin & User)
- **Guarantee Lifecycle Management**
  - Draft → Review → Apply → Issue → Reject
- **Bulk Upload Support**
  - Accepts CSV, JSON, XML
  - Sample file downloads + error reporting
- **File Management**
  - Secure upload, preview, and processing
- **Admin Dashboard**
  - Overview, pending reviews, and processing queues

---

## 🛠️ Technology Stack

- **Framework**: Laravel (PHP)
- **Frontend**: Blade + Bootstrap
- **Database**: MySQL
- **Containerization**: Podman / Docker
- **Authentication**: Laravel Breeze / Sanctum (as needed)

---

## ⚙️ System Requirements

- PHP 8.2+
- MySQL 8.0+
- Docker or Podman
- Node.js 18+ (for building assets)

---

## 🔧 Installation

### Podman/Docker (Recommended)

```bash
git clone https://github.com/kirangandhi97/TradeFinanceSystem.git
cd TradeFinanceSystem

# Start containers
podman-compose up -d

# Run migrations
podman exec -it trade_finance_app php artisan migrate

# Create test users
podman exec -it trade_finance_app php artisan tinker

\App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'role' => 'admin'
]);

\App\Models\User::create([
    'name' => 'Regular User',
    'email' => 'user@example.com',
    'password' => bcrypt('password'),
    'role' => 'user'
]);

Manual (Local) Setup
git clone https://github.com/kirangandhi97/TradeFinanceSystem.git
cd TradeFinanceSystem

composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate

# Update .env for DB connection
php artisan migrate
php artisan serve


Usage Guide
For Users
Login to your account

Create guarantees or upload files

Submit drafts for admin review

For Admins
Review & approve guarantees

Apply or reject with comments

Issue approved guarantees

Process uploaded files

Project Structure
app/Http/Controllers - All controllers

app/Models - Eloquent models

resources/views - Blade templates

routes/web.php - Main route file

database/migrations - Schema setup


Troubleshooting
Issue | Solution
File upload fails | Ensure format is CSV, JSON, or XML and size < 10MB
Database errors | Check DB config in .env and run php artisan migrate:fresh
Permission denied | Verify folder permissions (especially storage/ and bootstrap/cache/)


Deployment Notes
# Start
podman-compose up -d

# Stop
podman-compose down

# Logs
podman logs trade_finance_app


Database Backup
podman exec trade_finance_db \
  mysqldump -u root -pyour_password trade_finance_guarantee_issuance_system_production \
  > backup_$(date +%Y%m%d).sql


Contributing
Fork the repo

Create a new branch: git checkout -b feature/my-feature

Commit changes: git commit -am 'Add feature'

Push: git push origin feature/my-feature

Create a pull request

Acknowledgments
Laravel Framework

Bootstrap CSS

Docker / Podman

---

Let me know if you'd like:
- A matching `LICENSE.md` file
- Auto-generated badges (from GitHub Actions or Codecov)
- Project logo or banner to personalize the header

Want me to save this as a downloadable file for you?

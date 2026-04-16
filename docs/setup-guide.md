# Local Development Setup Guide

## Prerequisites

- **PHP 8.4+** with extensions: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- **Composer** (PHP dependency manager)
- **Node.js 18+** and **npm**
- **MySQL** (via WAMP, XAMPP, or standalone)
- **WAMP64** (recommended for Windows development)

## Step-by-Step Setup

### 1. Clone the Repository

```bash
git clone <repository-url> C:\wamp64\www\comm-calc
cd C:\wamp64\www\comm-calc
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set your database connection:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=comm-calc
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Create the Database

Open phpMyAdmin (http://localhost/phpmyadmin) or MySQL CLI and create the database:

```sql
CREATE DATABASE `comm-calc`;
```

### 5. Run Migrations and Seeders

```bash
php artisan migrate --seed
```

This creates all tables and seeds them with:
- 4 default users (see below)
- Commission settings (12 tier/threshold entries)
- SPIFF settings (9 incentive parameters)
- Default branding (Bayside Pavers)

### 5b. Load Demo Data (Optional)

To populate the system with realistic deals, commissions, weekly scores, and SPIFF payouts:

```bash
php artisan db:seed --class=DemoDataSeeder
```

### 6. Create Storage Symlink

```bash
php artisan storage:link
```

This allows uploaded logos to be publicly accessible.

### 7. Start Development Servers

```bash
npm run dev
```

In a separate terminal (or use `concurrently`):

```bash
php artisan serve
```

The app will be available at **http://localhost:8000** (or via WAMP at http://localhost/comm-calc/public).

## Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@baysidepavers.com | password |
| Manager | manager@baysidepavers.com | password |
| Sales Rep | john@baysidepavers.com | password |
| Sales Rep | jane@baysidepavers.com | password |

## Common Commands

| Command | Description |
|---------|-------------|
| `php artisan migrate` | Run pending migrations |
| `php artisan migrate:fresh --seed` | Reset database and re-seed |
| `php artisan db:seed --class=DemoDataSeeder` | Load demo deals, commissions, SPIFFs |
| `php artisan make:livewire ComponentName` | Create a new Livewire component |
| `npm run dev` | Start Vite dev server (hot reload) |
| `npm run build` | Build production assets |
| `php artisan test` | Run test suite |

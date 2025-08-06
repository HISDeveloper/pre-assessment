# Hargreaves App

A simple web app built with **Laravel (API)** and **Vue.js (Frontend)** using **Docker**.

## 🚀 Features

- User registration & login (via Sanctum)
- Product listing & categry filtering
- Shopping cart
- Order placement & history

## 🧰 Tech Stack

- Laravel 10 (API)
- Vue 3 + Bootstrap (Frontend)
- MySQL (Database)
- Mailtrap (Email Testing)
- Docker (Local Dev)

## ⚙️ Setup Instructions

### 1. Create .env files for both Laravel (/api) and Vue (/web)
```bash
cd into /api and /web directories
cp .env.example .env
```
Set your database, Mailtrap credentials, and other variables.

### 2. Setup with Docker
```bash
docker-compose up -d --build
```

### 3. Visit the app
- Frontend (Vue): http://localhost:8888
- API (Laravel): http://localhost:9999
- Database Admin (Adminer): http://localhost:9889
    - Server: db
    - Username: root
    - Password: secret

## ✉️ Mail Configuration
This app uses Mailtrap for email. Set these values in your api/.env (Laravel's env):

```bash
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_FROM_ADDRESS=no-reply@hargreaves.my
MAIL_FROM_NAME="Hargreaves App"
```

## Test Account
You can use the following default account to log in without going through registration and email verification:
```bash
Email:    admin@admin.com
Password: Admin@1234
```

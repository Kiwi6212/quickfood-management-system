# 🍽️ QuickFood - Secure Restaurant Management System

## 📌 Project Overview
**QuickFood** is a robust Full-Stack web application designed to digitize restaurant operations. It manages the entire customer flow, from account creation and table reservation to reviewing the dining experience.

> **Note**: This project has undergone major refactoring to meet professional security standards, including CSRF protection and environment variable management.

## ⚙️ Technical Architecture
* **Front-End**: Modular **HTML5**, **CSS3** (custom responsive grid), and **Vanilla JavaScript** (ES6 modules).
* **Back-End**: **PHP** (Native) with a modular architecture (Router/Controllers logic).
* **Database**: **MySQL** for relational data management.
* **Security**: Strict implementation of modern security protocols.

## 🛡️ Security Features (Highlights)
This project goes beyond basic functionality by implementing critical security measures:
* **CSRF Protection**: Implementation of anti-CSRF tokens to prevent Cross-Site Request Forgery attacks.
* **Environment Security**: Sensitive credentials (DB passwords, API keys) are managed via `.env` files (using `.env.example` for distribution), ensuring no secrets are exposed in the codebase.
* **XSS Prevention**: Strict output sanitization using `htmlspecialchars` to block Cross-Site Scripting.
* **Secure Authentication**: Use of `password_hash()` (Bcrypt/Argon2) for credential storage and session hijacking protection.

## ✨ Key Features
1.  **User Module**: Secure Signup/Login with feedback validation.
2.  **Booking Engine**: Real-time table reservation with conflict checking.
3.  **Admin Dashboard**: Protected interface for staff to manage bookings and view customer data.
4.  **Review System**: Client feedback loop protected against spam.

## 🚀 Setup & Installation
1.  Clone the repository.
2.  Rename `.env.example` to `.env` and configure your database credentials.
3.  Import the database schema (MySQL).
4.  Launch the server (e.g., via XAMPP or PHP built-in server).

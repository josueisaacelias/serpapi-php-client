# Modern SerpApi PHP Client (Unofficial Refactor)

A modern, PSR-4 compliant PHP client for SerpApi, built to demonstrate robust architecture and secure coding practices.

## 🚀 Key Improvements over Legacy SDK

| Feature | Legacy SDK | This Modern Implementation |
| :--- | :--- | :--- |
| **HTTP Client** | Custom Implementation (Fragile) | **Guzzle** (Standard & Robust) |
| **Security** | SSL Verification Disabled ❌ | **SSL Verification Enabled** ✅ |
| **Architecture** | Hardcoded dependencies | **Dependency Injection** |
| **Testing** | None / Manual | **PHPUnit + Mocking** |
| **Standards** | Custom `require` | **PSR-4 Autoloading** |

## 🛠️ Installation

```bash
composer require josueisaacelias/serpapi-php-client

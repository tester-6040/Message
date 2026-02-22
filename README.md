# PinkSecret Messenger (PHP MVC)

A no-framework private messenger with:
- Romantic landing page (`For You, My Heart`) as home
- Login/Register
- 1:1 conversations
- Encrypted message text storage
- Image/video sharing
- Typing indicator
- Online/Offline presence
- Browser notifications
- Pink professional UI with Tailwind CSS

## Stack
- PHP 8+
- MySQL
- Tailwind via CDN
- Vanilla JavaScript

## Quick Start (fully functional)
1. Copy environment file and edit secrets:
   ```bash
   cp .env.example .env
   ```
2. Import database schema:
   ```bash
   mysql -u root -p < database/schema.sql
   ```
3. Start app from repo root:
   ```bash
   php -S 0.0.0.0:8000 -t public
   ```
4. Open `http://localhost:8000/index.php`

## If you see "This site can’t be reached"
- Ensure PHP dev server is running in this repository:
  ```bash
  php -S 0.0.0.0:8000 -t public
  ```
- Verify health endpoint:
  ```bash
  curl -i http://127.0.0.1:8000/health.php
  ```

## Routes
- Landing: `/index.php`
- Login/Register: `/login.php`
- Chat: `/chat.php`
- API: `/api.php?action=...`
- Health check: `/health.php`

## API endpoints
Via `public/api.php?action=...`:
- `start-conversation` (POST)
- `messages` (GET)
- `send-message` (POST)
- `typing` (POST)
- `typing-status` (GET)
- `online-status` (GET)

All POST requests require `_csrf`.

## Production notes
- Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`.
- Set a strong `APP_ENCRYPTION_KEY`.
- Set `SESSION_SECURE_COOKIE=true` behind HTTPS.
- Use restrictive filesystem permissions for `public/uploads`.

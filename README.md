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

## Setup
1. Import database schema:
   ```bash
   mysql -u root -p < database/schema.sql
   ```
2. Update DB/app config in `config/config.php`.
3. Run server:
   ```bash
   php -S 0.0.0.0:8000 -t public
   ```
4. Open: `http://localhost:8000`
   - Landing page: `/index.php`
   - Login/Register: `/login.php`

## API endpoints
Via `public/api.php?action=...`:
- `start-conversation` (POST)
- `messages` (GET)
- `send-message` (POST)
- `typing` (POST)
- `typing-status` (GET)
- `online-status` (GET)

All POST requests require `_csrf`.

# PureIN Station Sales Panel

A small PHP/SQLite fuel sales panel for station managers.

## Task

The goal was to improve the existing panel by:

- Fixing security issues.
- Supporting Arabic and RTL.
- Making the sales table mobile-friendly.
- Formatting amounts correctly in SAR.
- Documenting the changes.

## Run

Requirements:

- PHP 8.1+
- PDO SQLite
- SQLite3

Run:

    php -S localhost:8000

Then open:

    http://localhost:8000

The database (`app.sqlite`) is created automatically on first run.

To reset it, delete `app.sqlite` and reload the application.

## Test Accounts

| Username | Password | Role |
|---|---|---|
| `admin` | `admin123` | Head Office |
| `manager_a` | `manager_a` | Station A |
| `manager_b` | `pass1234` | Station B |

## What I Changed

### Security

- Replaced plain-text password storage with `password_hash()` and `password_verify()`.
- Regenerated the session ID after login.
- Removed the password from session data.
- Replaced the unsafe station SQL query with a prepared statement.
- Restricted managers to their assigned station.
- Only admins can select different stations.
- Escaped dynamic HTML output with `htmlspecialchars()`.

### Arabic / RTL

- Changed the interface to Arabic.
- Added `lang="ar"` and `dir="rtl"`.
- Translated login and sales page labels.
- Adjusted layout and table alignment for RTL.

### Mobile

- Removed the fixed `1100px` table width.
- Made the table responsive.
- Added horizontal scrolling for narrow screens.
- Added the mobile viewport meta tag.
- Adjusted forms and controls for smaller screens.

### SAR

Amounts are now displayed with two decimal places, thousands separators, and the Saudi Riyal notation:

    1,234.56 ر.س

## Files Updated

- `db.php` — password hashing.
- `index.php` — secure login, sessions, Arabic/RTL.
- `sales.php` — authorization, prepared queries, Arabic/RTL, SAR.
- `style.css` — responsive/mobile layout.
- `logout.php` — no changes required.

## Verification

The local environment was checked with PHP 8.5.5 and:

    pdo_sqlite
    sqlite3

The main fixes were checked through the login flow, station authorization, prepared SQL queries, Arabic/RTL layout, mobile layout, and SAR formatting.


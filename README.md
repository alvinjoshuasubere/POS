# Modern PHP POS

A responsive, lightweight point-of-sale and inventory app built for **PHP 8.5.12+** and MySQL 8 / MariaDB. No framework or build tooling required.

## Features

- Username-only admin and cashier login, password hashing, session renewal and CSRF checks
- Point of sale with product search, category filter, cart, discount, cash/GCash/card/other payment, change calculation
- Database transactions and row locking to prevent overselling inventory
- Printable sale receipts, recent transactions and daily sales dashboard
- Inventory management: products, SKUs, categories, stock adjustments and low-stock alerts
- Custom store name, primary color and logo upload
- Mobile responsive interface, Philippine peso currency
- Admin-only inventory, appearance and staff management

## Requirements

- PHP **8.5.12 or newer** with PDO MySQL, mbstring and fileinfo
- MySQL 8+ or a compatible modern MariaDB installation
- Apache with PHP (recommended) and HTTPS for production
- Writable `uploads/` directory. Never allow PHP execution inside uploads.

PHP 8.6 was only in release-candidate status on November 12, 2026, so the project targets PHP 8.5, the latest stable series on that date. Actual PHP version is controlled by your hosting provider or server, not by GitHub files.

## Installation

1. Create a MySQL database and a restricted database user. For example:

   ```sql
   CREATE DATABASE pos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'pos_user'@'localhost' IDENTIFIED BY 'REPLACE_WITH_A_STRONG_PASSWORD';
   GRANT SELECT,INSERT,UPDATE,DELETE,CREATE,ALTER,INDEX,REFERENCES ON pos.* TO 'pos_user'@'localhost';
   ```

2. Upload the project to your PHP 8.5.12+ web host. If your web host is running an older PHP version, choose PHP 8.5 in your hosting panel or upgrade your server first. Enable HTTPS.
3. Copy `config.php` to `config.local.php`. Set `db_host`, `db_name`, `db_user`, `db_pass` and, if necessary, `db_port`. Do not commit `config.local.php`. You can alternatively set `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` environment variables.
4. Visit `https://your-domain/install.php` once to create the database tables and first admin account. Choose a username and an admin password of at least 6 characters.
5. **Immediately remove or restrict `install.php`** after successful installation. For tighter privileges, revoke schema-change privileges (`CREATE`, `ALTER`, `INDEX`, `REFERENCES`) from the app database user after setup.
6. Sign in at `index.php`. Add product categories and products, then stock quantities in Inventory. Go to Appearance to upload a logo or change your brand color. Use POS to complete your first sale.

## Security notes

- Use HTTPS, secure backups, a non-public database password, and a production web server.
- No default admin password or test credentials are shipped.
- The `uploads/.htaccess` file blocks executable uploads on Apache. If using Nginx, explicitly block execution of scripts within `/uploads/` and block access to `config.local.php`, `bootstrap.php` and `schema.sql` in your Nginx config.
- Use dedicated restricted DB credentials. This starter does not yet implement rate limiting, refunds/returns, audit log, tax invoices, receipt printer integration, barcode scanner hardware integration, or multi-branch management. Add these and perform security testing before use in a high-volume production environment.
- Store currency is currently PHP / Philippine pesos. Sales reflect local server database timezone. Configure your database/server timezone to `Asia/Manila`.

## Project files

- `index.php`: authenticated UI, inventory, POS, sales and receipt workflows
- `bootstrap.php`: database and shared security helpers
- `install.php`: one-time admin and schema setup
- `schema.sql`: relational MySQL tables
- `config.php`: example database configuration; edit local untracked copy
- `assets/style.css`: responsive design
- `assets/pos.js`: interactive checkout cart
- `migrations/001_username_login.sql`: one-time migration for existing installations. Back up the database, run the migration, then use usernames `user<ID>` (e.g. `user1`) for existing accounts until you change them manually in MySQL. Password hashes remain unchanged.

## Local checks

Run `php -l index.php && php -l bootstrap.php && php -l install.php`. Full checkout testing requires a PHP 8.5 server connected to MySQL.

## Product pictures and staff

Admins can add staff from **Staff**. The Add staff user form appears first, followed by the account list. Passwords must have at least 6 characters.

Admins can upload a PNG, JPG or WebP picture (maximum 2 MB) when creating or editing products in **Inventory**. The POS cards show these pictures. The uploads folder must be writable and block script execution.

**Existing installations:** back up the database and run `migrations/002_product_images.sql` once before using product pictures. New installations already have the image column in `schema.sql`.

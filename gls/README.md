# Graveyard Locator and Cemetery Mapping System
PHP 8 + MySQL (PDO). Tested layout for XAMPP/WAMP/Laragon or any PHP host.

1. Copy the folder to your web root as `graveyard-locator` (or change `BASE_URL` in `config.php`).
2. Import `database.sql` in phpMyAdmin / MySQL, then set DB credentials in `config.php`.
3. Open `/graveyard-locator/setup.php` to create the default admin (`admin / admin123`) and, if missing, a sample visitor (`visitor@example.com / user123`). It can add the admin even if other users already exist. Delete `setup.php` afterwards and change the admin password.
4. Site: `/graveyard-locator/public/index.php` — Admin: `/graveyard-locator/admin/login.php`.

Mapping: Admin > Cemetery Map. Pick a plot (or create one), click 3+ corner points on the map, press Save Plot Area.

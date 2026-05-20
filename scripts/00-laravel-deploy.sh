#!/usr/bin/env bash
echo "Running database migrations and seeders..."
php artisan migrate:fresh --seed --force

echo "Setting correct folder permissions for the web server..."
chown -R www-data:www-data /var/www/html/database /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/database /var/www/html/storage /var/www/html/bootstrap/cache

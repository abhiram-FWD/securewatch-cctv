#!/usr/bin/env bash
echo "Running database migrations and seeders..."
php artisan migrate:fresh --seed --force

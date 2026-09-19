#!/usr/bin/env bash
# Exit immediately if a command exits with a non-zero status
set -o errexit

echo "--- Starting Render Build and Setup Process ---"

# 1. Install Laravel backend dependencies without dev tools
echo "Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

# 2. Install frontend dependencies and build Vite / Vue assets
echo "Installing frontend dependencies and building assets..."
npm install
npm run build

echo "--- Optimizing Laravel Cache ---"
# 3. Cache configurations, routes, and views for speed and security
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "--- Running Migrations and Seeders ---"
# 4. Run database migrations and Spatie permissions seeder
php artisan migrate --force
php artisan db:seed --class=RolesAndPermissionsSeeder --force

echo "--- Build completed successfully and application is ready! ---"

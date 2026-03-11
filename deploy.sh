#!/bin/bash
cd domains/prod.live90.fr/public_html
cp .prod.env .env
php artisan optimize:clear
php artisan config:cache
php artisan migrate --force
echo "✅ Deployment Successful!"
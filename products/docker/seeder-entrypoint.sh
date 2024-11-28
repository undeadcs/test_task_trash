#!/usr/bin/env sh
set -e

php artisan db:seed

exec "$@"

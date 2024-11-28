#!/usr/bin/env sh
set -e

# TODO: remove after migrator ready
php artisan migrate

php artisan optimize:clear
php artisan optimize

exec "$@"

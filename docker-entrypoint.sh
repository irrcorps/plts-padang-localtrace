#!/bin/sh
set -e

# Render menyediakan port dinamis lewat env var $PORT. Apache di image PHP
# resmi defaultnya listen di port 80, jadi kita ganti saat container start.
PORT="${PORT:-8080}"

sed -ri "s/Listen [0-9]+/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -ri "s/:80>/:${PORT}>/g" /etc/apache2/sites-enabled/000-default.conf

exec "$@"

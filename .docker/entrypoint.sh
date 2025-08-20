#!/usr/bin/env bash
set -e

# Default PORT if not provided (useful for local docker run)
: "${PORT:=9000}"

# Substitute $PORT into nginx template
envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf

# Show final config (helps during debugging)
echo "---- Rendered /etc/nginx/conf.d/default.conf ----"
cat /etc/nginx/conf.d/default.conf
echo "--------------------------------------------------"

exec "$@"

#!/usr/bin/env sh
set -e

APP_DIR="${APP_DIR:-/app}";

STARTUP_CLEAR_CACHE="${STARTUP_CLEAR_CACHE:-false}"

/app/docker/wait-for "${POSTGRES_HOST}":"${POSTGRES_PORT}" -t 240 -- echo "PostgreSQL is ready."

if [ "${STARTUP_CLEAR_CACHE}" = "true" ]; then
    echo "[INFO] Starting cache cleanup";
    php bin/console cache:pool:clear cache.app;
    php bin/console cache:pool:clear cache.system;
    php bin/console cache:pool:clear cache.validator;
    php bin/console cache:pool:clear cache.serializer;
    php bin/console cache:pool:clear doctrine.result_cache_pool;
    php bin/console cache:pool:clear doctrine.system_cache_pool;
    echo "[INFO] Cache cleanup is completed";
fi;

exec "$@";
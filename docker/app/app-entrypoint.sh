#!/usr/bin/env sh
set -e

APP_DIR="${APP_DIR:-/app}";

STARTUP_CLEAR_CACHE="${STARTUP_CLEAR_CACHE:-false}"
STARTUP_START_CONSUMERS="${STARTUP_START_CONSUMERS:-true}"
STARTUP_START_SUPERVISORD="${STARTUP_START_SUPERVISORD:-true}"

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

if [ "${STARTUP_START_SUPERVISORD}" = "true" ]; then
    echo "[INFO] Start supervisord";
    /usr/bin/supervisord -c /etc/supervisor/supervisord.conf;
    echo "[INFO] Start supervisord finished";
fi;

if [ "${STARTUP_START_CONSUMERS}" = "true" ]; then
    echo "[INFO] Start consumers process";
    /usr/bin/supervisorctl start message-workers:*;
    echo "[INFO] Start consumers process finished";
fi;

exec "$@";
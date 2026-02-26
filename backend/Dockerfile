# syntax=docker/dockerfile:1
# ─── Stage 1: Build (PHP 8.2 + Composer) ────────────────────────────────────
# IMPORTANTE: builder y runtime deben usar la misma versión de PHP para que
# el platform_check.php generado por Composer sea compatible.
FROM php:8.2-cli AS builder

RUN apt-get update && apt-get install -y \
        libpq-dev zip unzip git curl \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Copiar solo el binario de Composer (no la imagen entera)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Crear proyecto Laravel 10 (platform check quedará fijado en PHP 8.2)
RUN composer create-project laravel/laravel:^10.0 . --no-interaction --prefer-dist

# Copiar archivos personalizados (sobreescriben los de Laravel)
COPY backend/app/            app/
COPY backend/database/       database/
COPY backend/config/cors.php config/cors.php
COPY backend/routes/api.php  routes/api.php

# ─── Stage 2: Runtime ────────────────────────────────────────────────────────
FROM php:8.2-cli

# Dependencias del sistema
RUN apt-get update && apt-get install -y \
        libpq-dev \
        zip unzip git \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/app

# Copiar el proyecto construido desde el stage anterior
COPY --from=builder /app .

# Configurar permisos
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Generar .env inline (evita COPY del contexto de build)
RUN cat <<'ENVFILE' > .env
APP_NAME="Gestor de Tareas"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=gestor_tareas
DB_USERNAME=postgres
DB_PASSWORD=secret

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

CORS_ALLOWED_ORIGINS=http://localhost:5173
ENVFILE
# Crear entrypoint inline (evita depender del contexto de build)
RUN cat <<'ENTRYPOINT' > /entrypoint.sh
#!/bin/bash
set -e

echo "==> Inyectando variables de Railway en .env..."
[ -n "$DB_HOST" ]              && sed -i "s|DB_HOST=.*|DB_HOST=${DB_HOST}|"                                         .env
[ -n "$DB_PORT" ]              && sed -i "s|DB_PORT=.*|DB_PORT=${DB_PORT}|"                                         .env
[ -n "$DB_DATABASE" ]          && sed -i "s|DB_DATABASE=.*|DB_DATABASE=${DB_DATABASE}|"                             .env
[ -n "$DB_USERNAME" ]          && sed -i "s|DB_USERNAME=.*|DB_USERNAME=${DB_USERNAME}|"                             .env
[ -n "$DB_PASSWORD" ]          && sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=${DB_PASSWORD}|"                             .env
[ -n "$CORS_ALLOWED_ORIGINS" ] && sed -i "s|CORS_ALLOWED_ORIGINS=.*|CORS_ALLOWED_ORIGINS=${CORS_ALLOWED_ORIGINS}|" .env

echo "==> Esperando que PostgreSQL acepte conexiones..."
until php -r "
    \$conn = @fsockopen(
        getenv('DB_HOST') ?: 'postgres',
        getenv('DB_PORT') ?: 5432,
        \$errno, \$errstr, 3
    );
    if (\$conn) { fclose(\$conn); exit(0); }
    exit(1);
" 2>/dev/null; do
    echo "    PostgreSQL no disponible, reintentando en 2s..."
    sleep 2
done

echo "==> PostgreSQL disponible. Ejecutando migraciones..."
php artisan migrate --force

echo "==> Iniciando servidor Laravel en 0.0.0.0:${PORT:-8000}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
ENTRYPOINT
RUN chmod +x /entrypoint.sh

# Generar APP_KEY
RUN php artisan key:generate --force

EXPOSE 8000

ENTRYPOINT ["/entrypoint.sh"]

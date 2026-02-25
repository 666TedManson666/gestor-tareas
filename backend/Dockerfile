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
COPY app/            app/
COPY database/       database/
COPY config/cors.php config/cors.php
COPY routes/api.php  routes/api.php

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

# Copiar archivo de entorno y entrypoint
COPY .env.example .env
COPY entrypoint.sh /entrypoint.sh
# Eliminar saltos de línea Windows (CRLF → LF) y dar permisos de ejecución
RUN sed -i 's/\r$//' /entrypoint.sh && chmod +x /entrypoint.sh

# Generar APP_KEY
RUN php artisan key:generate --force

EXPOSE 8000

ENTRYPOINT ["/entrypoint.sh"]

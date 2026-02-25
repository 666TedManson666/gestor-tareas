#!/bin/bash
set -e

echo "==> Esperando que PostgreSQL acepte conexiones..."
# Usar PHP puro para testear la conexión TCP (no depende de pg_isready)
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

echo "==> Iniciando servidor Laravel en 0.0.0.0:8000..."
exec php artisan serve --host=0.0.0.0 --port=8000

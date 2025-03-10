#!/bin/bash
set -e

# Gerar chave do aplicativo se não existir
php artisan key:generate --force

# Limpar e recriar o cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Otimizar
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Criar link simbólico do storage
php artisan storage:link

# Executar migrações do banco de dados
php artisan migrate --force

# Iniciar Apache em primeiro plano
exec "$@" 
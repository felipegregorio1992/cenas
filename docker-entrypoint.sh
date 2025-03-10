#!/bin/bash

# Aguardar o MySQL estar pronto
until php artisan db:monitor > /dev/null 2>&1; do
  echo "🟡 Aguardando conexão com o banco de dados..."
  sleep 1
done

# Instalar dependências
composer install --no-interaction --no-progress

# Gerar chave da aplicação se não existir
php artisan key:generate --no-interaction

# Executar migrações e seeders
php artisan migrate --force
php artisan db:seed --force

# Limpar cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "✅ Aplicação inicializada com sucesso!"

# Executar o comando passado como argumento (php-fpm)
exec "$@" 
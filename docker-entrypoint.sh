#!/bin/bash
set -e

# Aguardar o MySQL estar pronto
echo "Aguardando conexão com o banco de dados..."
maxTries=10
while [ $maxTries -gt 0 ]; do
    if php artisan db:monitor > /dev/null 2>&1; then
        break
    fi
    maxTries=$(($maxTries - 1))
    sleep 3
done

if [ $maxTries -eq 0 ]; then
    echo "Não foi possível conectar ao banco de dados."
    exit 1
fi

echo "Conexão com o banco de dados estabelecida."

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
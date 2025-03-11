#!/bin/bash
set -e

echo "Configurando ambiente..."

# Criar arquivo .env com as variáveis de ambiente
echo "APP_NAME=\"${APP_NAME}\"" > .env
echo "APP_ENV=${APP_ENV}" >> .env
echo "APP_KEY=${APP_KEY}" >> .env
echo "APP_DEBUG=${APP_DEBUG}" >> .env
echo "APP_URL=${APP_URL}" >> .env
echo "DB_CONNECTION=${DB_CONNECTION}" >> .env
echo "DB_HOST=${DB_HOST}" >> .env
echo "DB_PORT=${DB_PORT}" >> .env
echo "DB_DATABASE=${DB_DATABASE}" >> .env
echo "DB_USERNAME=${DB_USERNAME}" >> .env
echo "DB_PASSWORD=${DB_PASSWORD}" >> .env
echo "BROADCAST_DRIVER=${BROADCAST_DRIVER}" >> .env
echo "CACHE_DRIVER=${CACHE_DRIVER}" >> .env
echo "FILESYSTEM_DRIVER=${FILESYSTEM_DRIVER}" >> .env
echo "QUEUE_CONNECTION=${QUEUE_CONNECTION}" >> .env
echo "SESSION_DRIVER=${SESSION_DRIVER}" >> .env
echo "SESSION_LIFETIME=${SESSION_LIFETIME}" >> .env

# Função para testar conexão com o banco
test_db_connection() {
    php -r "
    try {
        \$dsn = 'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT');
        \$conn = new PDO(
            \$dsn,
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD'),
            [PDO::ATTR_TIMEOUT => 5]
        );
        echo 'Conexão bem sucedida';
        return 0;
    } catch(PDOException \$e) {
        echo \$e->getMessage();
        return 1;
    }
    "
}

# Aguardar o MySQL estar pronto
echo "Aguardando conexão com o banco de dados..."
echo "DB_HOST: $DB_HOST"
echo "DB_PORT: $DB_PORT"
echo "DB_DATABASE: $DB_DATABASE"

maxTries=10
while [ $maxTries -gt 0 ]; do
    if test_db_connection; then
        break
    fi
    maxTries=$(($maxTries - 1))
    echo "Tentativas restantes: $maxTries"
    sleep 5
done

if [ $maxTries -eq 0 ]; then
    echo "Erro: Não foi possível conectar ao banco de dados após várias tentativas."
    exit 1
fi

echo "Conexão com o banco de dados estabelecida."

# Gerar chave do aplicativo se não existir
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Limpar cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Executar migrações
php artisan migrate --force

# Otimizar
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Criar link simbólico do storage
php artisan storage:link || true

# Ajustar permissões
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

echo "Iniciando Apache..."
exec apache2-foreground 
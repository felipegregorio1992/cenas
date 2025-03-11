#!/bin/bash
set -e

echo "Configurando ambiente..."

# Verificar variáveis obrigatórias
required_vars=(
    "DB_CONNECTION"
    "DB_HOST"
    "DB_PORT"
    "DB_DATABASE"
    "DB_USERNAME"
    "DB_PASSWORD"
)

for var in "${required_vars[@]}"; do
    if [ -z "${!var}" ]; then
        echo "Erro: Variável de ambiente $var não está definida"
        exit 1
    fi
done

# Criar arquivo .env com as variáveis de ambiente
cat << EOF > .env
APP_NAME="${APP_NAME:-Linha do Tempo}"
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL}

LOG_CHANNEL=${LOG_CHANNEL:-stack}
LOG_LEVEL=${LOG_LEVEL:-error}

DB_CONNECTION=${DB_CONNECTION}
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}

BROADCAST_DRIVER=${BROADCAST_DRIVER:-log}
CACHE_DRIVER=${CACHE_DRIVER:-file}
FILESYSTEM_DRIVER=${FILESYSTEM_DRIVER:-local}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-sync}
SESSION_DRIVER=${SESSION_DRIVER:-file}
SESSION_LIFETIME=${SESSION_LIFETIME:-120}

MEMCACHED_HOST=${MEMCACHED_HOST:-127.0.0.1}

REDIS_HOST=${REDIS_HOST:-127.0.0.1}
REDIS_PASSWORD=${REDIS_PASSWORD:-null}
REDIS_PORT=${REDIS_PORT:-6379}
EOF

# Função para testar conexão com o banco
test_db_connection() {
    php -r "
    try {
        \$dsn = 'pgsql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE');
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

# Aguardar o PostgreSQL estar pronto
echo "Aguardando conexão com o banco de dados..."
echo "Configurações do banco de dados:"
echo "DB_CONNECTION: $DB_CONNECTION"
echo "DB_HOST: $DB_HOST"
echo "DB_PORT: $DB_PORT"
echo "DB_DATABASE: $DB_DATABASE"
echo "DB_USERNAME: $DB_USERNAME"

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
    echo "Gerando nova APP_KEY..."
    php artisan key:generate --force
fi

echo "Limpando cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo "Executando migrações..."
php artisan migrate --force

echo "Otimizando..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Criando link simbólico do storage..."
php artisan storage:link || true

echo "Ajustando permissões..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

echo "Iniciando Apache..."
exec apache2-foreground 
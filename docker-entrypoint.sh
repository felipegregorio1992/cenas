#!/bin/bash
set -e

echo "Configurando ambiente..."

# Definir valores padrão para variáveis críticas
export DB_CONNECTION=${DB_CONNECTION:-pgsql}
export DB_HOST=${DB_HOST:-dpg-cv7nv52n91rc739dju2g-a.oregon-postgres.render.com}
export DB_PORT=${DB_PORT:-5432}
export DB_DATABASE=${DB_DATABASE:-linha_do_tempo}
export DB_USERNAME=${DB_USERNAME:-linha_do_tempo_user}
export DB_PASSWORD=${DB_PASSWORD:-4E5z58jML5i3WywHYCqLgsWMiPI40Cji}

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
CACHE_DRIVER=database
CACHE_STORE=database
FILESYSTEM_DRIVER=${FILESYSTEM_DRIVER:-local}
QUEUE_CONNECTION=database
SESSION_DRIVER=database
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

# Função para verificar se uma tabela existe
table_exists() {
    local table=$1
    php -r "
    try {
        \$pdo = new PDO(
            'pgsql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD')
        );
        \$stmt = \$pdo->query(\"SELECT to_regclass('public.$table')\");
        \$exists = \$stmt->fetchColumn();
        exit(\$exists ? 0 : 1);
    } catch(Exception \$e) {
        exit(1);
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

echo "Verificando e criando tabelas do sistema..."

# Verifica e cria a tabela de migrações se necessário
if ! table_exists "migrations"; then
    echo "Criando tabela de migrações..."
    php artisan migrate:install
else
    echo "Tabela de migrações já existe."
fi

# Verifica e cria a tabela de cache se necessário
if ! table_exists "cache"; then
    echo "Criando tabela de cache..."
    php artisan cache:table
    php artisan migrate --force
else
    echo "Tabela de cache já existe."
fi

# Verifica e cria a tabela de sessões se necessário
if ! table_exists "sessions"; then
    echo "Criando tabela de sessões..."
    php artisan session:table
    php artisan migrate --force
else
    echo "Tabela de sessões já existe."
fi

# Verifica e cria a tabela de jobs se necessário
if ! table_exists "jobs"; then
    echo "Criando tabela de jobs..."
    php artisan queue:table
    php artisan migrate --force
else
    echo "Tabela de jobs já existe."
fi

echo "Executando migrações pendentes..."
php artisan migrate --force

echo "Limpando cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

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
#!/bin/bash
set -e

# Função para testar conexão com o banco
test_db_connection() {
    php -r "
    try {
        \$dbh = new PDO(
            'mysql:host=${DB_HOST};port=${DB_PORT}',
            '${DB_USERNAME}',
            '${DB_PASSWORD}'
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
    echo "Host: $DB_HOST"
    echo "Porta: $DB_PORT"
    echo "Usuário: $DB_USERNAME"
    exit 1
fi

echo "Conexão com o banco de dados estabelecida."

# Gerar chave do aplicativo se não existir
php artisan key:generate --force

# Limpar cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Criar banco de dados se não existir
php artisan db:create --if-not-exists

# Executar migrações
php artisan migrate --force

# Otimizar
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Criar link simbólico do storage
php artisan storage:link

# Ajustar permissões
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache

# Iniciar Apache
apache2-foreground 
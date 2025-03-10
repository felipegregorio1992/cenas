FROM php:8.2-apache

# Instalar dependências
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev

# Limpar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Habilitar mod_rewrite e headers
RUN a2enmod rewrite headers

# Configurar Apache
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/apache2.conf \
    && sed -i 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar composer.json e composer.lock primeiro
COPY composer.json composer.lock ./

# Instalar dependências
RUN composer install --no-scripts --no-autoloader --no-dev

# Copiar o resto dos arquivos do projeto
COPY . .

# Gerar autoloader otimizado
RUN composer dump-autoload --optimize --no-dev

# Copiar arquivo de configuração do Apache
COPY apache.conf /etc/apache2/sites-available/000-default.conf
RUN ln -sf /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-enabled/000-default.conf

# Criar diretórios necessários e definir permissões
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage \
    && chmod -R 775 bootstrap/cache \
    && chmod -R 775 public

# Configurar variáveis de ambiente PHP
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini

# Copiar .env.production para .env
COPY .env.production .env

# Gerar chave do aplicativo e otimizar
RUN php artisan key:generate --force \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan storage:link

# Expor porta
EXPOSE ${PORT}

# Iniciar Apache
CMD ["apache2-foreground"] 
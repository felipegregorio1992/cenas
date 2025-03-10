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

# Habilitar mod_rewrite
RUN a2enmod rewrite

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

# Definir permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Configurar variáveis de ambiente PHP
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini

# Expor porta
EXPOSE ${PORT}

# Iniciar Apache
CMD ["apache2-foreground"] 
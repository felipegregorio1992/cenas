FROM php:8.2-fpm

# Argumentos
ARG user=laravel
ARG uid=1000

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    libzip-dev

# Instalar extensão Redis
RUN pecl install redis && docker-php-ext-enable redis

# Limpar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar diretório de trabalho
WORKDIR /var/www

# Criar usuário do sistema e configurar permissões
RUN groupadd -g $uid $user \
    && useradd -u $uid -g $user -s /bin/sh -m $user

# Criar script de inicialização
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Copiar arquivos do projeto
COPY . .

# Configurar permissões
RUN chown -R $user:$user /var/www \
    && chmod -R 755 /var/www \
    && chmod -R 777 /var/www/storage /var/www/bootstrap/cache \
    && git config --system --add safe.directory /var/www

# Expor porta 9000
EXPOSE 9000

# Definir o script de entrada
ENTRYPOINT ["docker-entrypoint.sh"]

# Iniciar PHP-FPM
CMD ["php-fpm"] 
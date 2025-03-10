FROM php:8.2-fpm

# Argumentos
ARG user=www-data
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
    npm

# Limpar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Criar diretório do sistema
RUN mkdir -p /var/www

# Criar usuário do sistema e configurar permissões
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user && \
    chown -R $user:$user /var/www

# Definir diretório de trabalho
WORKDIR /var/www

# Copiar permissões do usuário existente
COPY --chown=$user:$user . /var/www

# Mudar para o usuário não-root
USER $user

# Expor porta 9000
EXPOSE 9000

CMD ["php-fpm"] 
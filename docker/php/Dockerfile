FROM php:8.3-fpm

# تثبيت الحزم الأساسية
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# تنظيف الكاش
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# تثبيت إضافات PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# جلب Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# إنشاء يوزر ليتطابق مع اليوزر في نظام أوبونتو
ARG user
ARG uid
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

WORKDIR /var/www/html

USER $user
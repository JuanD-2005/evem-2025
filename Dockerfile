# Usamos una imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instalamos las extensiones necesarias para conectarnos a MariaDB/MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Habilitamos el módulo rewrite de Apache (muy útil para URLs limpias)
RUN a2enmod rewrite

# Damos permisos a la carpeta del servidor
RUN chown -R www-data:www-data /var/www/html

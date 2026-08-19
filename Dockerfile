# LokalTrust - PHP Native 8.x on Apache (Docker)
# Render.com tidak punya runtime PHP native, jadi dideploy sebagai Web Service
# berbasis Docker. Tidak ada perubahan pada kode aplikasi/struktur database.
FROM php:8.2-apache

# Ekstensi PDO MySQL (fileinfo sudah built-in di image PHP resmi)
RUN docker-php-ext-install pdo pdo_mysql

# Aktifkan mod_rewrite untuk clean URL (/login, /verify/{code}, dst) dan
# izinkan .htaccess (folder config/core/models/controllers/views memakainya
# untuk memblokir akses langsung dari browser)
RUN a2enmod rewrite \
    && sed -ri -e 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf

WORKDIR /var/www/html
COPY . /var/www/html/

# Folder upload foto produk harus writable oleh Apache
RUN chown -R www-data:www-data /var/www/html/uploads \
    && chmod -R 775 /var/www/html/uploads

# Render menyuntikkan env var $PORT secara dinamis saat runtime; Apache
# defaultnya listen di port 80, jadi disesuaikan lewat entrypoint.
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8080
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]

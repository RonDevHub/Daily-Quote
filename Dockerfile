FROM php:8.3-apache

# 1. Apache-Module aktivieren (mod_rewrite für .htaccess)
RUN a2enmod rewrite headers

# 2. Arbeitsverzeichnis im Container festlegen
WORKDIR /var/www/html

# 3. Projektdateien in den Container kopieren
# Entspricht der Struktur: data/, public/, src/ landen direkt in /var/www/html/
COPY data/ ./data/
COPY public/ ./public/
COPY src/ ./src/

# 4. Apache DocumentRoot auf das public-Verzeichnis umbiegen
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# 5. Besitzer und Rechte explizit setzen (wichtig für Linux-Hosts und Docker-Mounts)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod 644 /var/www/html/data/quotes.php

# Apache im Vordergrund starten
CMD ["apache2-foreground"]
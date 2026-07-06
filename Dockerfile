FROM php:8.3-apache

# 1. Apache-Module aktivieren (mod_rewrite und mod_headers)
RUN a2enmod rewrite headers

# 2. Arbeitsverzeichnis festlegen
WORKDIR /var/www/html

# 3. Dateien direkt mit den korrekten Rechten für den Webserver-Nutzer kopieren
# Das verhindert Rechte-Konflikte nach dem Build via Kaniko vollständig.
COPY --chown=www-data:www-data data/ ./data/
COPY --chown=www-data:www-data public/ ./public/
COPY --chown=www-data:www-data src/ ./src/

# 4. Apache DocumentRoot auf das public-Verzeichnis umbiegen
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# 5. Absicherung der Dateiberechtigungen im Container
RUN chmod -R 755 /var/www/html \
    && chmod 644 /var/www/html/data/quotes.php

# Apache im Vordergrund starten
CMD ["apache2-foreground"]
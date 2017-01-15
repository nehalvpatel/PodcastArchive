FROM tutum/lamp:latest

ADD apache-config.conf /etc/apache2/sites-enabled/000-default.conf

COPY needl_pka.sql /var/www/
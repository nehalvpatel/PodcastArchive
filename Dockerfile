FROM tutum/lamp:latest

ADD apache-config.conf /etc/apache2/sites-enabled/000-default.conf

COPY sql/data.sql /var/www/
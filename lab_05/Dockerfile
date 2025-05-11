FROM debian:latest

VOLUME /var/lib/mysql

VOLUME /var/log

RUN apt-get update && \
    apt-get install -y apache2 php libapache2-mod-php php-mysql mariadb-server supervisor curl tar && \
    apt-get clean

ADD https://wordpress.org/latest.tar.gz /tmp/
RUN tar -xzf /tmp/latest.tar.gz -C /tmp/ && \
    rm /tmp/latest.tar.gz && \
    mv /tmp/wordpress/* /var/www/html/ && \
    rm -rf /tmp/wordpress

COPY files/apache2/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY files/apache2/apache2.conf /etc/apache2/apache2.conf

COPY files/php/php.ini /etc/php/8.2/apache2/php.ini

COPY files/mariadb/50-server.cnf /etc/mysql/mariadb.conf.d/50-server.cnf

COPY files/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN mkdir /var/run/mysqld && chown mysql:mysql /var/run/mysqld

EXPOSE 80

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

COPY files/wp-config.php /var/www/html/wordpress/wp-config.php
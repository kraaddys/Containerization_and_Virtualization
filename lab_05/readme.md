# Лабораторная работа №5. Запуск сайта в контейнере

## Студент

**Славов Константин, группа I2302**  
**Дата выполнения: _23.03.2025_**

## Цель работы

Выполняя данную работу мне предстоит подготовить образ контейнера для запуска веб-сайта на базе **Apache HTTP Server** + **PHP (mod_php)** + **MariaDB**.

## Задание

Создать **Dockerfile** для сборки образа контейнера, который будет содержать веб-сайт на базе **Apache HTTP Server** + **PHP (mod_php)** + **MariaDB**. База данных MariaDB должна храниться в монтируемом томе. Сервер должен быть доступен по порту 8000. Установить сайт WordPress. Проверить работоспособность сайта.

## Ход работы

**1. Создание и клонирование репозитория `containers05` на свой компьютер.**

**2. Извлечение конфигурационных файлов **apache2**, **php**, **mariadb** из контейнера.**

В папке `containers05` я создал новую папку `files`, в которой создал:

- папку files/apache2 - для файлов конфигурации apache2;
- папку files/php - для файлов конфигурации php;
- папку files/mariadb - для файлов конфигурации mariadb.

После чего в папке `containers05` я создал `Dockerfile` со следующим содержимым:

```sh
FROM debian:latest
```

а также

```sh
RUN apt-get update && \
    apt-get install -y apache2 php libapache2-mod-php php-mysql mariadb-server && \
    apt-get clean
```

Затем я занялся созданием образа контейнера с именем `apache2-php-mariadb`.

И после этого я создал контейнер `apache2-php-mariadb` из образа `apache2-php-mariadb` и запустил его в фоновом режиме с командой запуска `bash`.

Следующими действиями стали копирование файлов конфигурации apache2, php, mariadb из контейнера в папку `files/` на компьютере. Для этого, в контексте проекта, выполнил команды:

```sh
docker cp apache2-php-mariadb:/etc/apache2/sites-available/000-default.conf files/apache2/
docker cp apache2-php-mariadb:/etc/apache2/apache2.conf files/apache2/
docker cp apache2-php-mariadb:/etc/php/8.2/apache2/php.ini files/php/
docker cp apache2-php-mariadb:/etc/mysql/mariadb.conf.d/50-server.cnf files/mariadb/
```

После их выполнения, в папке `files/` появились файлы конфигурации apache2, php, mariadb.

![image](https://i.imgur.com/CFCWALK.jpeg)

Затем я остановил и удалил контейнер `apache2-php-mariadb`.

**3. Настройка конфигурационных файлов**

## Конфигурационный файл apache2:

- Открыл файл `files/apache2/000-default.conf`, затем нашел строку `#ServerName www.example.com` и заменил её на `ServerName localhost`.

- Далее нашел строку `ServerAdmin webmaster@localhost` и заменил в ней почтовый адрес на свой.

- После строки `DocumentRoot /var/www/html` добавил следующие строки:

`DirectoryIndex index.php index.html`

![image](https://i.imgur.com/PqP6kcj.jpeg)

Сохранил изменения в данном файле и закрыл его.

- В конце файла `files/apache2/apache2.conf` добавил следующую строку:

`ServerName localhost`

![image](https://i.imgur.com/VnxGZk5.jpeg)

## Конфигурационный файл php:

Открыл файл `files/php/php.ini`, нашел строку `;error_log = php_errors.log` и заменил её на `error_log = /var/log/php_errors.log`.

После настроил параметры **memory_limit**, **upload_max_filesize**, **post_max_size** и **max_execution_time** следующим образом:

```ini
memory_limit = 128M
upload_max_filesize = 128M
post_max_size = 128M
max_execution_time = 120
```

Сохранил изменения в данном файле и закрыл его.

## Конфигурационный файл mariadb:

- Открыл файл `files/mariadb/50-server.cnf`, нашел строку `#log_error = /var/log/mysql/error.log` и раскомментировал её.

Сохранил изменения в данном файле и закрыл его.

**4. Создание скрипта запуска**

- Создал в папке `files` папку `supervisor` и файл `supervisord.conf` со следующим содержимым:

```sh
[supervisord]
nodaemon=true
logfile=/dev/null
user=root

# apache2
[program:apache2]
command=/usr/sbin/apache2ctl -D FOREGROUND
autostart=true
autorestart=true
startretries=3
stderr_logfile=/proc/self/fd/2
user=root

# mariadb
[program:mariadb]
command=/usr/sbin/mariadbd --user=mysql
autostart=true
autorestart=true
startretries=3
stderr_logfile=/proc/self/fd/2
user=mysql
```

![image](https://i.imgur.com/Wha743v.jpeg)

**5. Создание Dockerfile**

Открыл файл `Dockerfile` и добавил в него следующие строки:

- после инструкции FROM ... добавьте монтирование томов:

```sh
# mount volume for mysql data
VOLUME /var/lib/mysql

# mount volume for logs
VOLUME /var/log
```

- в инструкции RUN ... добавьте установку пакета supervisor:

```sh
RUN apt-get update && \
    apt-get install -y apache2 php libapache2-mod-php php-mysql mariadb-server supervisor curl tar && \
    apt-get clean
```

- после инструкции RUN ... добавьте копирование и распаковку сайта WordPress:

```sh
ADD https://wordpress.org/latest.tar.gz /tmp/
RUN tar -xzf /tmp/latest.tar.gz -C /tmp/ && \
    rm /tmp/latest.tar.gz && \
    mv /tmp/wordpress/* /var/www/html/ && \
    rm -rf /tmp/wordpress
```

- после копирования файлов WordPress добавьте копирование конфигурационных файлов apache2, php, mariadb, а также скрипта запуска:

```sh
# copy the configuration file for apache2 from files/ directory
COPY files/apache2/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY files/apache2/apache2.conf /etc/apache2/apache2.conf

# copy the configuration file for php from files/ directory
COPY files/php/php.ini /etc/php/8.2/apache2/php.ini

# copy the configuration file for mysql from files/ directory
COPY files/mariadb/50-server.cnf /etc/mysql/mariadb.conf.d/50-server.cnf

# copy the supervisor configuration file
COPY files/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
```

- для функционирования mariadb создайте папку /var/run/mysqld и установите права на неё:

```sh
# create mysql socket directory
RUN mkdir /var/run/mysqld && chown mysql:mysql /var/run/mysqld
```

- откройте порт 80:

```sh
EXPOSE 80
```

- добавьте команду запуска supervisord:

```sh
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
```

**6. Создание базы данных и пользователя**

Создал базу данных `wordpress` и пользователя **wordpress** с паролем **wordpress** в контейнере `apache2-php-mariadb`. Для этого, в контейнере `apache2-php-mariadb`, выполнил команды:

`mysql`

```sql
CREATE DATABASE wordpress;
CREATE USER 'wordpress'@'localhost' IDENTIFIED BY 'wordpress';
GRANT ALL PRIVILEGES ON wordpress.* TO 'wordpress'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

![image](https://i.imgur.com/CtCSs4f.jpeg)

**7. Создание файла конфигурации WordPress**

Открыл в браузере сайт WordPress по адресу `http://localhost/`. Указал параметры подключения к базе данных:

- имя базы данных: **wordpress**;
- имя пользователя: **wordpress**;
- пароль: **wordpress**;
- адрес сервера базы данных: **localhost**;
- префикс таблиц: **wp_**.

![image](https://i.imgur.com/ugWfz99.jpeg)

Скопировал содержимое файла конфигурации в файл `files/wp-config.php` на компьютере.

![image](https://i.imgur.com/LcBndwl.jpeg)

**8. Добавление файла конфигурации WordPress в Dockerfile**

Добавил в файл **Dockerfile** следующие строки:

```sh
COPY files/wp-config.php /var/www/html/wordpress/wp-config.php
```

**9. Запуск и тестирование**

Пересобрал образ контейнера с именем `apache2-php-mariadb` и запустил контейнер `apache2-php-mariadb` из образа `apache2-php-mariadb`. Проверил работоспособность сайта WordPress.

![image](https://i.imgur.com/msITiFa.jpeg)

![image](https://i.imgur.com/tMLcVi2.jpeg)

![image](https://i.imgur.com/zRwewwH.jpeg)

## Ответы на вопросы

**_1. Какие файлы конфигурации были изменены?_**

Изменены следующие файлы:

- files/apache2/000-default.conf – основной виртуальный хост Apache

- files/apache2/apache2.conf – глобальная конфигурация Apache

- files/php/php.ini – конфигурация PHP

- files/mariadb/50-server.cnf – настройки MariaDB

- files/supervisor/supervisord.conf – автозапуск Apache и MariaDB

- files/wp-config.php – конфигурация WordPress

**_2. За что отвечает инструкция DirectoryIndex в файле конфигурации apache2?_**

Инструкция DirectoryIndex указывает, какой файл Apache должен загружать по умолчанию, если пользователь обращается к каталогу.

**_3. Зачем нужен файл wp-config.php?_**

Файл wp-config.php содержит основные настройки WordPress, включая:

- параметры подключения к базе данных (DB_NAME, DB_USER, DB_PASSWORD, DB_HOST)

- префикс таблиц

- ключи безопасности и другие параметры

Без этого файла WordPress не может работать.

**4. _За что отвечает параметр `post_max_size` в файле конфигурации PHP?_**

Параметр post_max_size определяет максимальный размер всех данных, отправляемых методом POST. Это включает формы, файлы и другие данные.

**_5. Укажите, на ваш взгляд, какие недостатки есть в созданном образе контейнера?_**

Возможные недостатки:

- База данных не инициализируется автоматически. Требуется вручную заходить в контейнер и создавать БД.

- Нет docker-compose.yml. Все команды запуска выполняются вручную, что неудобно.

- WordPress скачивается при каждой сборке образа. Лучше использовать COPY или внешний том.

- Данные теряются при удалении контейнера, если volume не подключён. Нужно сразу подключать volume для базы и /var/www/html.

- Безопасность, т.е. конфиг wp-config.php и пароль БД хранятся в образе без защиты.

## Выводы

В ходе выполнения лабораторной работы был разработан и успешно запущен Docker-контейнер на базе образа Debian, включающий в себя веб-сервер Apache2, интерпретатор PHP, систему управления базами данных MariaDB, а также менеджер процессов Supervisor. Внутри контейнера была установлена и настроена CMS WordPress, произведена её начальная конфигурация и проверена работоспособность через браузер. В результате были получены практические навыки работы с Docker, настройки серверной инфраструктуры и внедрения систем управления контентом.

## Библиография

- [Docker Documentation](https://docs.docker.com/get-started/overview/)
- [Apache HTTP Server Documentation](https://httpd.apache.org/docs/2.4/)
- [PHP Manual](https://www.php.net/manual/en/ini.core.php)
- [MariaDB Documentation](https://mariadb.com/kb/en/mariadb-documentation/)
- [WordPress Documentation](https://wordpress.org/support/article/how-to-install-wordpress/)
- [Supervisor Documentation](http://supervisord.org/)
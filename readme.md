# Лабораторная работа №1. Виртуальный сервер

## Студент

**Славов Константин, группа I2302**
**Дата выполнения: _14.02.2025_**

## Описание задачи

Данная лабораторная работа знакомит нас с особенностями создания виртуального HTTP-сервера, его настройки и работы с ним.
В ходе работы будет установлена виртуальная машина на базе Debian с гипервизором QEMU, также развернут LAMP, затем будет произведена
установка PhpMyAdmin и Drupal. Напоследок, проведем настройку виртуальных хостов Apache и завершим установку сайтов.

## Ход работы

### Подготовка компонентов

1. Скачиваю и устанавливаю на компьютер MSYS2

    ![image](https://i.imgur.com/wHaBYv5.jpeg)

2. После установки MSYS2, я создал папку `CV` (инициалы курса), после чего уже в этой папке я создал новую - `lab01`
2.1. Затем перехожу в папку `lab01` и там создаю папку `dvd` и файл `readme.md`. Я решил все это сделать вручную через проводник для упрощения процесса
3. После всех проделанных действий я зашел на официальный сайт **Debian** и загружаю на компьютер `Iso-образ первого DVD для 64-битного ПК`

    ![image](https://i.imgur.com/unEG0PS.jpeg)

4. После, захожу в папку `dvd` и в консоли выполняю команду для скачивания файла

    ```sh
    wget -O debian.iso https://cdimage.debian.org/debian-cd/current/amd64/iso-dvd/debian-12.9.0-amd64-DVD-1.iso
    ```

    `-0` - параметр, который сохраняет загруженный образ как debian.iso

5. И затем скачиваю и устанавливаю **QEMU** командой, которая была указана на официальном сайте

    ```sh
    pacman -S mingw-w64-ucrt-x86_64-qemu
    ```

6. После всех проделанных действий, **QEMU**, наконец, установлен на наш ПК

    ![image](https://i.imgur.com/Ov7Lg7G.jpeg)

### Установка операционной системы Debian на виртуальную машину

1. Первым делом, я выполняю в консоли следующую команду. Это нужно для того, чтобы создать образ диска для нашей виртуальной машины размером 8 ГБ в формате qcow2:

    ```sh
    qemu-img create -f qcow2 debian.qcow2 8G
    ```

2. Вот что из этого вышло:

    ![image](https://i.imgur.com/6sAA4vS.jpeg)

3. Далее, я выполнил запуск установки ОС **Debian** следующей командой:

    ```sh
    qemu-system-x86_64 -hda debian.qcow2 -cdrom dvd/debian.iso -boot d -m 2G
    ```

4. У меня все запустилось с первого раза и я начал установку ОС на свою виртуальную машину.

    ![image](https://i.imgur.com/bBBZguW.jpeg)

5. Установку Debian я производил с указанными ранее параметрами:
    - Имя компьютера: **debian**;
    - Хостовое имя: **debian.localhost**;
    - Имя пользователя: **user**;
    - Пароль пользователя: **password**;

6. Во время процесса установки на этапе Software Selection я выбрал для установки только `standard system utilities`, таким образом установив систему без графического интерфейса и рабочего стола.

### Запуск виртуальной машины

1. После установки ОС на виртуальную машину, я произвел ее запуск при помощи следующей команды, выделяя ей 2 ГБ RAM и 2 ядра процессора.
Она подключает виртуальную сетевую карту и пробрасывает порт 80 (веб-сервер) и порт 22 (SSH) так, что можно зайти на `http://localhost:1080` и попасть на веб-сервер внутри ВМ.

    ```sh
    qemu-system-x86_64 -hda debian.qcow2 -m 2G -smp 2 \
    -device e1000,netdev=net0 -netdev user,id=net0,hostfwd=tcp::1080-:80,hostfwd=tcp::1022-:22
    ```

2. После проделанных действий, система начала загрузку:

    ![image](https://i.imgur.com/iSbTXdE.jpeg)

    ![image](https://i.imgur.com/mS3awMg.jpeg)

### Установка LAMP

1. Перед всеми последующими манипуляциями, я первым делом перехожу в режим суперпользователя

    ```sh
    su
    ```

2. Затем, для корректной работы последующих команд я добавил в файл `sources.etc` ссылки на некоторые репозитории. Это делается для того, чтобы при последующих манипуляциях у нас не выскакивали какие-либо ошибки.

3. Чтобы начать редактирование файла, нужно прописать следующее:

    ```sh
    nano /etc/apt/sources.list
    ```

    после чего я добавил в него следующие ссылки:

    ```
    deb http://deb.debian.org/debian bookworm main non-free
    deb-src http://deb.debian.org/debian bookworm main non-free

    deb http://deb.debian.org/debian bookworm-updates main non-free
    deb-src http://deb.debian.org/debian bookworm-updates main non-free

    deb http://security.debian.org/debian-security bookworm-security main non-free
    deb-src http://security.debian.org/debian-security bookworm-security main non-free
    ```

    ![image](https://i.imgur.com/SoUvKxC.jpeg)

    Затем я сохранил все изменения.

4. Далее я ввел команды для обновления списка пакетов и их установки

    ```sh
    apt update -y
    apt install -y apache2 php libapache2-mod-php php-mysql mariadb-server mariadb-client unzip
    ```

    Краткое объяснение назначения установленных пакетов:

    `apache2` - веб-сервер Apache для обработки HTTP-запросов.  
    `php` - интерпретатор PHP, необходимый для выполнения PHP-скриптов.  
    `libapache2-mod-php` - модуль для интеграции PHP с Apache, позволяющий серверу запускать PHP-код.  
    `php-mysql` - расширение PHP для взаимодействия с базами данных MySQL/MariaDB.  
    `mariadb-server` - сервер базы данных MariaDB, являющийся форком MySQL.  
    `mariadb-client` - клиент для подключения и работы с сервером MariaDB.  
    `unzip` - утилита для распаковки файлов в формате ZIP.

### Установка PhpMyAdmin и CMS Drupal

1. Я использовал следующие команды для скачивания PhpMyAdmin и CMS Drupal:

    ```sh
    wget https://files.phpmyadmin.net/phpMyAdmin/5.2.2/phpMyAdmin-5.2.2-all-languages.zip
    wget https://ftp.drupal.org/files/projects/drupal-11.1.1.zip
    ```

    ![image](https://i.imgur.com/CIWvB0i.jpeg)

    ![image](https://i.imgur.com/5CRwsVK.jpeg)

2. Проверка наличия файлов в системе:

    ```sh
    ls -l
    ```

    ![image](https://i.imgur.com/FvA3e4R.jpeg)

3. Распаковка и перемещение файлов:

    ```sh
    mkdir /var/www
    unzip phpMyAdmin-5.2.2-all-languages.zip
    mv phpMyAdmin-5.2.2-all-languages /var/www/phpmyadmin
    unzip drupal-10.0.5.zip
    mv drupal-10.0.5 /var/www/drupal
    ```

    ![image](https://i.imgur.com/Sk67NIE.jpeg)

    ![image](https://i.imgur.com/GsTC4dW.jpeg)

    ![image](https://i.imgur.com/jc6kdCK.jpeg)

### Настройка базы данных

1. Создаю базу данных drupal_db для CMS через командную строку и пользователя базы данных с именем `user`

    Ввожу следующий набор команд:

    ```sh
    mysql -u root
    CREATE DATABASE drupal_db;
    CREATE USER 'user'@'localhost' IDENTIFIED BY 'password';
    GRANT ALL PRIVILEGES ON drupal_db.* TO 'daniil'@'localhost';
    FLUSH PRIVILEGES;
    EXIT;
    ```

    Вот как это выглядит на деле:

    ![image](https://i.imgur.com/UDVduDL.jpeg)

    ![image](https://i.imgur.com/1Hxk9Af.jpeg)

    ![image](https://i.imgur.com/T2vSTau.jpeg)

    ![image](https://i.imgur.com/cGixQ8L.jpeg)

    ![image](https://i.imgur.com/VwXh9t4.jpeg)

    Таким образом, база данных была успешно создана.

### Настройка виртуальных хостов Apache

1. Для начала создаю файлы конфигурации

    - Начну с PhpMyAdmin:

    `nano /etc/apache2/sites-available/01-phpmyadmin.conf`

    После чего записываю:

    ```
    <VirtualHost *:80>
        ServerAdmin webmaster@localhost
         DocumentRoot "/var/www/phpmyadmin"
         ServerName phpmyadmin.localhost
         ServerAlias www.phpmyadmin.localhost
         ErrorLog "/var/log/apache2/phpmyadmin.localhost-error.log"
         CustomLog "/var/log/apache2/phpmyadmin.localhost-access.log" common
    </VirtualHost>
    ```

    Вот, что из этого получилось:

    ![image](https://i.imgur.com/eG7qr05.jpeg)

    ![image](https://i.imgur.com/UGoj2ZM.jpeg)

    - То же самое проделываю и для CMS Drupal:

    `nano /etc/apache2/sites-available/02-drupal.conf`

    ```
      <VirtualHost *:80>
         ServerAdmin webmaster@localhost
         DocumentRoot "/var/www/drupal"
         ServerName drupal.localhost
         ServerAlias www.drupal.localhost
         ErrorLog "/var/log/apache2/drupal.localhost-error.log"
         CustomLog "/var/log/apache2/drupal.localhost-access.log" common
      </VirtualHost>
    ```

    Результат следующих действий:

    ![image](https://i.imgur.com/R7EG7ZO.jpeg)

    ![image](https://i.imgur.com/qunThri.jpeg)
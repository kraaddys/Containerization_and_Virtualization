# Лабораторная работа №9: Оптимизация образов контейнеров

## Студент

**Славов Константин, группа I2302**  
**Дата выполнения: _27.04.2025_**

## Цель работы

Целью данной лабораторной работы является знакомство с методами оптимизации образов.

## Задание

Сравнить различные методы оптимизации образов:

- Удаление неиспользуемых зависимостей и временных файлов
- Уменьшение количества слоев
- Минимальный базовый образ
- Перепаковка образа
- Использование всех методов

## Ход работы

**1. Создал репозиторий `containers09` и склонировал его к себе на компьютер. В папке `containers09` создал папку `site` и поместил туда файлы сайта, который я использовал для предыдущих лабораторных работ.**

![image](https://i.imgur.com/lLcjE0v.png)

**2. Для оптимизации использовал образ определенный следующим `Dockerfile.raw`**

```sh
# create from ubuntu image
FROM ubuntu:latest

# update system
RUN apt-get update && apt-get upgrade -y

# install nginx
RUN apt-get install -y nginx

# copy site
COPY site /var/www/html

# expose port 80
EXPOSE 80

# run nginx
CMD ["nginx", "-g", "daemon off;"]
```

Все это я создал в папке `containers09`, затем собрал в образ `mynginx:raw`

```sh
docker image build -t mynginx:raw -f Dockerfile.raw .
```

![image](https://i.imgur.com/hxGVdaA.png)

## Удаление неиспользуемых зависимостей и временных файлов

**1. Удалил временные файлы и неиспользуемые зависимости в `Dockerfile.clean`**

```sh
# create from ubuntu image
FROM ubuntu:latest

# update system
RUN apt-get update && apt-get upgrade -y

# install nginx
RUN apt-get install -y nginx

# remove apt cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# copy site
COPY site /var/www/html

# expose port 80
EXPOSE 80

# run nginx
CMD ["nginx", "-g", "daemon off;"]
```

**2. Собрал образ с именем `mynginx:clean` и проверил его размер:**

```sh
docker image build -t mynginx:clean -f Dockerfile.clean .
docker image list
```

Размер образа `mynginx:clean` составляет 177 МБ.

![image](https://i.imgur.com/Bf4dnTO.png)
![image](https://i.imgur.com/nnAqFXu.png)

## Уменьшение количества слоев

**1. Уменьшил количество слоев в `Dockerfile.few`**

```sh
# create from ubuntu image
FROM ubuntu:latest

# update system
RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y nginx && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# copy site
COPY site /var/www/html

# expose port 80
EXPOSE 80

# run nginx
CMD ["nginx", "-g", "daemon off;"]
```

**2. Собрал образ с именем `mynginx:few` и проверил его размер:**

```sh
docker image build -t mynginx:few -f Dockerfile.few .
docker image list
```

Размер образа `mynginx:few` составляет 128 МБ.

![image](https://i.imgur.com/h9BVF10.png)
![image](https://i.imgur.com/jnTRZgC.png)

## Минимальный базовый образ

**1. Заменил базовый образ на `alpine` и пересобрал образ:**

```sh
# create from alpine image
FROM alpine:latest

# update system
RUN apk update && apk upgrade

# install nginx
RUN apk add nginx

# copy site
COPY site /var/www/html

# expose port 80
EXPOSE 80

# run nginx
CMD ["nginx", "-g", "daemon off;"]
```

**2. Собрал образ с именем `mynginx:alpine` и проверил его размер:**

```sh
docker image build -t mynginx:alpine -f Dockerfile.alpine .
docker image list
```

Размер образа `mynginx:alpine` составляет 14.8 МБ.

![image](https://i.imgur.com/ZWmNb9u.png)
![image](https://i.imgur.com/aKJ9aJz.png)

## Перепаковка образа

**1. Перепаковал образ `mynginx:raw` в `mynginx:repack`:**

```sh
docker container create --name mynginx mynginx:raw
docker container export mynginx | docker image import - mynginx:repack
docker container rm mynginx
docker image list
```

![image](https://i.imgur.com/PtDPJE8.png)

## Использование всех методов

**1. Создал образ `mynginx:min` с использованием всех методов:**

```sh
# create from alpine image
FROM alpine:latest

# update system, install nginx and clean
RUN apk update && apk upgrade && \
    apk add nginx && \
    rm -rf /var/cache/apk/*

# copy site
COPY site /var/www/html

# expose port 80
EXPOSE 80

# run nginx
CMD ["nginx", "-g", "daemon off;"]
```

**2. Собрал образ с именем `mynginx:minx` и проверил его размер. Перепаковал образ `mynginx:minx` в `mynginx:min`:**

```sh
docker image build -t mynginx:minx -f Dockerfile.min .
docker container create --name mynginx mynginx:minx
docker container export mynginx | docker image import - myngin:min
docker container rm mynginx
docker image list
```

Размер образа `mynginx:min` составляет 12.3 МБ.

![image](https://i.imgur.com/fkylz2q.png)

## Запуск и тестирование

**1. Проверил размеры образов:**

```sh
docker image list
```

![image](https://i.imgur.com/AtTRv9d.png)

## Таблица с размерами образов

## Сравнительная таблица размеров образов

| Название образа   | Размер  | Комментарий                                           |
|-------------------|---------|-------------------------------------------------------|
| mynginx:raw        | 177MB   | Исходный образ без оптимизации                       |
| mynginx:clean      | 177MB   | Очистка в отдельном слое — без эффекта                |
| mynginx:few        | 128MB   | Объединение в один слой уменьшило размер              |
| mynginx:alpine     | 14.8MB  | Использование минимального базового образа            |
| mynginx:repack     | 137MB   | Перепаковка без изменения функциональности            |
| mynginx:minx       | 12.3MB  | Максимальная оптимизация — alpine + очистка           |
| mynginx:min        | 12.3MB  | Перепаковка оптимизированного образа (alpine + очистка) |

## Ответы на вопросы

**Какой метод оптимизации образов вы считаете наиболее эффективным?**

Наиболее эффективным методом оптимизации Docker-образов является **использование минимального базового образа** вместе с **удалением временных файлов и кэша в рамках одного слоя**.
Минимальные базовые образы, такие как `alpine`, имеют очень маленький размер по сравнению с полноценными образами вроде `ubuntu` или `debian`, поскольку содержат только самые необходимые библиотеки и пакеты. Это позволяет значительно сократить итоговый размер образа и уменьшить потенциальные уязвимости, связанные с лишними пакетами.
Дополнительно удаление кэшированных данных, временных файлов и ненужных пакетов помогает уменьшить размер ещё сильнее, особенно если это сделано на этапе одного объединенного слоя, чтобы не сохранять лишние изменения в истории образа.
Таким образом, **наилучший результат достигается комбинацией двух подходов:** использование минимального базового образа и удаление ненужных данных в одном слое.

**Почему очистка кэша пакетов в отдельном слое не уменьшает размер образа?**

В Docker каждый `RUN`-инструктаж создает **новый слой**, который сохраняет изменения по сравнению с предыдущим слоем.
Когда выполняется команда установки пакетов (`apt-get install` или `apk add`) — создается слой, в котором уже сохранены загруженные файлы кэша.
Если потом в новом слое выполнить очистку (`apt-get clean` или `rm -rf /var/cache/apk/*`), то, хотя новые файлы действительно удаляются, **прошлый слой с кэшированными файлами всё равно остается внутри истории образа**.
Docker не умеет "перезаписывать" или "сжимать" старые слои — он просто добавляет новый слой поверх старого, а старый слой занимает место.
Поэтому если очистка кэша производится **в отдельном слое**, это **не уменьшает фактический размер** итогового образа.
Чтобы реально уменьшить размер, очистку необходимо объединить с установкой пакетов **в одном** `RUN` через объединение команд с помощью `&&`.

**Что такое перепаковка образа?**

Перепаковка образа — это процесс создания нового Docker-образа на основе существующего контейнера путём его экспорта и последующего импорта. В этом процессе сначала экспортируется файловая система запущенного контейнера в виде архивного файла, а затем этот архив импортируется обратно в Docker как новый образ. Такой подход позволяет объединить все изменения, накопленные в контейнере, в один слой, устранив историю промежуточных слоёв, которые обычно сохраняются при обычной сборке образа. Перепакованный образ становится компактнее, упрощается его структура, а также уменьшается общий размер, поскольку в нем отсутствуют лишние данные и промежуточные слои установки, удаления и обновления компонентов. Перепаковка особенно полезна для оптимизации образов, повышения скорости их загрузки и развёртывания, а также для повышения безопасности за счёт удаления ненужных следов операций.

## Вывод

В процессе выполнения лабораторной работы были изучены различные методы оптимизации Docker-образов. Было продемонстрировано, что удаление временных файлов и неиспользуемых зависимостей помогает уменьшить размер образа, однако наибольший эффект достигается, если объединять команды установки и очистки в одном слое, избегая накопления ненужных данных в истории слоёв.
Использование минимального базового образа, такого как Alpine, позволило существенно сократить размер итогового контейнера без потери базовой функциональности. Это подтвердило, что выбор подходящего базового образа является одним из ключевых факторов оптимизации.
Перепаковка образов через экспорт и импорт контейнера также показала свою эффективность, позволяя объединить все изменения в один слой и дополнительно уменьшить объём образа.
Таким образом, оптимизация Docker-образов напрямую влияет на скорость развертывания приложений, экономию дискового пространства и улучшение безопасности за счёт удаления лишних компонентов. Наиболее эффективным подходом является комплексное применение всех изученных методов.

## Библиография

- [Docker Documentation – Best practices for writing Dockerfiles](https://docs.docker.com/develop/develop-images/dockerfile_best-practices/) - Официальная документация Docker с рекомендациями по созданию оптимизированных и безопасных Dockerfile, включая советы по уменьшению числа слоев, очистке временных файлов и использованию минимальных базовых образов.
- [Docker Documentation – docker export](https://docs.docker.com/engine/reference/commandline/export/) - Официальное описание команды `docker container export`, которая позволяет сохранить файловую систему контейнера для последующего импорта и создания нового образа.
- [Docker Documentation – docker import](https://docs.docker.com/engine/reference/commandline/import/) - Официальное руководство по использованию команды `docker image import` для создания образов из экспортированных файловых систем контейнеров.
- [DockerCon Presentation – Slimmer Docker images](https://www.docker.com/blog/small-containers-faster-deployments/) - Статья на официальном блоге Docker, объясняющая важность создания маленьких образов для повышения скорости развертывания приложений и снижения расходов на хранение и передачу данных.
- [Alpine Linux Documentation](https://wiki.alpinelinux.org/wiki/Docker) - Официальная документация по использованию дистрибутива Alpine Linux в контейнерах Docker, объясняющая преимущества минимальных образов для оптимизации приложений.

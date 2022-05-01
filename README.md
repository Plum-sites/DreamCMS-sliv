<h1 align="center">
DreamCMS<br>
Сайт на Laravel (в простонородии ларки) и Vue, специально под Minecraft.
</h1>

<h3 align="center">
**Не проверен на работоспособность!**
</h3>

Ссылка на инструкцию от автора: https://dreamcms.beshelmek.org/books/dreamcms/page/ustanovka

____

О сливе:
 - Студия/Автор: [Beshelmek](https://github.com/Beshelmek/)
 - Попал: Чистенькая от доброго самаритянина :slightly_smiling_face:
____

В комплекте:
 - Сайт
 - база данных
____

Обязательные требования:
 - Nginx - вебсервер
 - PHP v7.4+ и PHP-FPM
 - NodeJS v12.x и NPM
 - Composer v2+ - менеджер пакетов PHP
 - MariaDB (v13+) - MySQL сервер базы данных
 - Redis - кеш-сервер
 - ZIP / UNZIP (установка: apt install -y zip unzip)

Заивисимости PHP:
 - BCMath
 - Ctype
 - Fileinfo
 - JSON
 - Mbstring
 - PDO
 - Tokenizer
 - XML
 - GD
 - CURL

Сначала, обновите пакеты:
```
apt update
apt upgrade
```

Установите необходимые компоненты для установки:
```
apt -y install lsb-release apt-transport-https ca-certificates sudo software-properties-common dirmngr curl
```

Установка PHP, PHP-FPM и необходимых пакетов:
```
wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | sudo tee /etc/apt/sources.list.d/php.list
apt update
apt -y install php7.4
apt -y install php7.4-{bcmath,ctype,fileinfo,json,mbstring,mysql,pdo,zip,tokenizer,gd,curl,dom}
```

Устанавливаем NGINX и LetsEncrypt:
```
apt -y install nginx certbot python3-certbot-nginx
```

Устанавливаем MariaDB и Redis:
```
apt-key adv --fetch-keys 'https://mariadb.org/mariadb_release_signing_key.asc'
add-apt-repository 'deb [arch=amd64] http://mirrors.ukfast.co.uk/sites/mariadb/repo/10.5/debian buster main'
apt update
apt -y install mariadb-server
mysql_secure_installation
apt -y install redis-server
```

Установка NodeJS и NPM:
```
curl -sL https://deb.nodesource.com/setup_12.x | sudo -E bash -
apt -y install nodejs
```

Дополнительно:
 - .env.example - Это ваш конфиг (уберите .example или создайте новый файл '.env')

<h2 align="center">
<br>
Сайт в архиве | В папке можно посмотреть внутренности<br>
Непосредственно слив самого CubaCraft находится в соответствующей папке<br>
Раздел будет редактироваться в зависимости от необходимости
</h2>

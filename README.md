<h1 align="center">
DreamCMS<br>
Сайт на Laravel (в простонородии ларки) и Vue, специально под Minecraft.
</h1>

<h3 align="center">
**Не проверен на работоспособность!**
</h3>

____

О сливе:
 - Студия/Автор: [Beshelmek](https://github.com/Beshelmek/)
 - Попал: Слив из CubaCraft.ru(На текущий момент, недоступен)(И как бонус, чистенькая от доброго самаритянина :slightly_smiling_face:)
____

В комплекте:
 - Сайт
 - база данных
____

Обязательные требования:
 - redis_server
 - MariaDB 10.3 и выше
 - Node 12
 - php 7.4 и выше
 - - BCMath PHP Extension
 - - Ctype PHP Extension
 - - Fileinfo PHP extension
 - - JSON PHP Extension
 - - Mbstring PHP Extension
 - - OpenSSL PHP Extension
 - - PDO PHP Extension
 - - Tokenizer PHP Extension
 - - XML PHP Extension
 - - GD PHP Extension
 - - Curl PHP Extension
 - Apache и/или Nginx

Команды для установки большей части требований:
```
apt-get install nginx php7.4-fpm

apt-get install php7.4-curl php7.4-bcmath php7.4-mbstring php7.4-pdo php7.4-tokenizer php7.4-xml php7.4-mysql php7.4-gd
```

Если не встаёт из за дебиан, прописать это а потом то что выше:
```
apt-get update && apt-get upgrade

apt -y install lsb-release apt-transport-https ca-certificates

wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg

echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | sudo tee /etc/apt/sources.list.d/php.list
```
Node 12 уже есть в композере...

Дополнительно:
 - .env.example - Это ваш конфиг (уберите .example или создайте новый файл '.env')

<h2 align="center">
<br>
Сайт в архиве | В папке можно посмотреть внутренности<br>
Непосредственно слив самого CubaCraft находится в соответствующей папке<br>
Раздел будет редактироваться в зависимости от необходимости
</h2>

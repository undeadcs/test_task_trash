[charset=UTF-8, line-ending=\n]

Необходимо предварительно создать базу данных, чтобы не тупил драйвер
mysql -uusername -p
...вводим пароль
create database treetest default charset utf8 default collate utf8_general_ci;

Затем настраиваем config.php
прописать все что нужно для подключения к СУБД

Запускаем установщик, который выполнит scheme.sql
php install.php

Импортируем дерево из categories.json
php import.php

После можно запускать алгоритмы:
задача №1
php export-a.php
cat type_a.txt

задача №2
php export-b.php
cat type_b.txt

задача №3
php type_menu.php > test.html
firefox test.html
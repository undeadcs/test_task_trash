# Products

Сервис отдачи каталога товаров.

* Сборка: `docker compose build api seeder`
* Запуск: `docker compose up -d`.
* Наполнение базы: `docker compose up seeder`

По умолчанию сервис доступен по адресу `http://127.0.0.1:14243`, список товаров: `http://127.0.0.1:14243/products`.  
Перед запуском необходимо создать api.env в директории docker (`cp .env docker/api.env`) и прописать там настройки подключения к базе данных:

```
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=products
DB_USERNAME=root
DB_PASSWORD=example
```

# Autostorage API

Проект веб-сервиса Автосклад.
Основан на библиотеке ReactPHP HTTP

## Зависимости
"react/http": "^1.0" <https://github.com/reactphp/http>  
"nikic/fast-route": "^1.3" <https://github.com/nikic/FastRoute>  
"vlucas/phpdotenv": "^5.1" <https://github.com/vlucas/phpdotenv>  
"respect/validation": "^2.0" <https://github.com/Respect/Validation>  
"react/filesystem": "^0.1.2" <https://github.com/reactphp/filesystem>  
"narrowspark/mimetypes": "^1.6" <https://github.com/narrowspark/mimetypes>  
"firebase/php-jwt": "^5.2" <https://github.com/firebase/php-jwt>  
"voryx/pgasync": "^2.0" <https://github.com/voryx/PgAsync>  
"react/cache": "^1.0" <https://github.com/reactphp/cache>  
"willdurand/hateoas": "^3.6" <https://github.com/willdurand/Hateoas>  

## Установка

### Конфигурация:

В файле: 
```
.env
```
Заполните поля (пример):
```
DB_NAME=autostorage
DB_USER=postgres
DB_PASS=postgres
DB_HOST=localhost
DB_DRIVER=pdo_pgsql
DB_PORT=5533
JWT_KEY=3d7fb4673c4db8b33353dffc0f5de0a8797986f770a2c963f270e01028151b84
```
***DB_PORT*** - порт базы данных
***DB_USER*** - пользователь базы данных для подключения
***DB_PASS*** - пароль пользователя для подключения
***DB_NAME*** - имя базы
***JWT_KEY*** - секретный ключ JWT

## База данных
Восстановите дамп из файла в корне приложения

## Установка библиотек
Из корвевой папки проекта запустите:
```
composer install
```
## Запуск
Из корневой папки проекта запустите:
```
php main.php
```
Сервер доступен по адресу: <http://localhost:8000>

## Доступ пользователей
Администратор: login: admin pass:123456  
Менеджер: login: manager pass:123456  
Кладовщик: login: storekeeper pass:123456  

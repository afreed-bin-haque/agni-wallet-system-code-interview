# Agni Wallet (Interview code)

Greetings,
this this a document about how to run the project
copy from .interview.env and paste it into .env

To run the project :

```
composer install
php artisan migrate
composer run dev
```

Redis Closed env docker port:

```
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PORT=8379
REDIS_PASSWORD=null
```

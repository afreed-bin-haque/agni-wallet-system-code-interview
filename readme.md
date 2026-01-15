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
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PORT=8379
REDIS_PASSWORD=null
```

- Docker
  For Redis IMG (Port - 8379)

```
docker run -d \
  --name redis-8379 \
  --restart unless-stopped \
  -p 127.0.0.1:8379:6379 \
  -v redis_8379_data:/data \
  redis:7.4-alpine \
  redis-server --appendonly yes

```

For GOTENBERG IMG (Port - 8088)

```
docker run -d \
  --name gotenberg-8088 \
  --restart unless-stopped \
  -p 127.0.0.1:8088:3000 \
  gotenberg/gotenberg:7
```

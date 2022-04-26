## Mypromo Connect SDK Test Script
Test script built in laravel for playaround

### Tests

```
Tests are in app/Console/Commands/TestSdk.php
```


### Install

```
git clone git@github.com:MyPromoConnect/connect-sdk-testing.git
compsoer install
docker-compose up
```


### Sample env
```
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:ZVGtKGZaUtZfc19CsVP3SpHe7s4jEb2vTsjJq64RP/I=
APP_DEBUG=true
APP_URL=http://localhost
APP_PORT=3001

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

CONNECT_ENDPOINT_URL=https://oma.dev.api.mypromo.com
CONNECT_CLIENT_ID=CONNECT_CLIENT_ID
CONNECT_CLIENT_SECRET=CONNECT_CLIENT_SECRET
CONNECT_SHOP_URL=https://oma.dev.connect.mypromo.com/

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DRIVER=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=memcached

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=null
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

```



### FIRST START APP:
##### install symfony-cli:
```bash
echo 'deb [trusted=yes] https://repo.symfony.com/apt/ /' | sudo tee /etc/apt/sources.list.d/symfony-cli.list
sudo apt update
sudo apt install symfony-cli
```

install mysql extension 
```
apt install php7.4-mysql 

```

create database 

```
 php bin/console doctrine:database:create
 php bin/console doctrine:migrations:migrate
```

сгенерировать API key https://www.cryptocompare.com/cryptopian/api-keys
\App\Service\API\Api::API_KEY (при необходимости заменить API key)

### RUN
```bash
composer install
symfony server:start
```

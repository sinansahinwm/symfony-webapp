# DigitalOcean - App Platform Configuration

## -- When Dev
### Build Command

~~~
composer install
npm run build
php bin/console doctrine:schema:drop --force
php bin/console doctrine:schema:create
php bin/console doctrine:migrations:migrate --dry-run
php bin/console cache:clear
php bin/console doctrine:fixtures:load --append
cd assets/puppeteer_replayer && npm install && cd ~
~~~

### Run Command

~~~
heroku-php-apache2 & node ~/assets/puppeteer_replayer/server.js & php bin/console messenger:consume async
~~~

### Then Console


# -- When Prod

TODO : todo
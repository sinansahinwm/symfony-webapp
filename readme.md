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
nohup heroku-php-apache2 > app_heroku.out
cd assets/puppeteer_replayer
nohup node server.js > app_node.out
cd ~
nohup php bin/console messenger:consume async > app_messenger
~~~

# -- When Prod

TODO : todo
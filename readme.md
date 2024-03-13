# DigitalOcean - App Platform Configuration

## -- When Dev
### Build Command

~~~
composer install
npm install
npm run build
php bin/console doctrine:schema:drop --force
php bin/console doctrine:schema:create
php bin/console doctrine:migrations:migrate --dry-run
php bin/console cache:clear
php bin/console doctrine:fixtures:load --append
cd assets/server && npm install
cd assets/server/apps/puppeteer_replayer && npm install
~~~

### Run Command

~~~
heroku-php-apache2 & node assets/server/server.js & php bin/console messenger:consume async
~~~

# -- When Prod

TODO : todo
# Symfony Web Application Boilerplate

To run your app, follow steps below.

1. Create Database
2. Configure Application
3. Build & Release Application
4. Run Application

## Build & Deploy

1. Build Symfony's Web App

~~~
# Install back-end dependencies.
composer install

# Install front-ent dependencies
npm install

# Build npm dependencies
npm run build

# Install symfony's 3rd party assets
php bin/console assets:install public

# Drop database scheme (if exist)
php bin/console doctrine:schema:drop --force

# Create database scheme
php bin/console doctrine:schema:create

# Run all migrations & migrate database
php bin/console doctrine:migrations:migrate --dry-run

# Clear symfony's cache
php bin/console cache:clear

# Load symfony's fixtures
php bin/console doctrine:fixtures:load --append

~~~

## Consuming Async Messages

~~~
php bin/console messenger:consume async
php bin/console messenger:consume scheduler_default
~~~

## Errors And Solutions

1. Data truncated column error. Its causing because enum types. you can fix this error via add this enums manually. For example;

~~~

ALTER TABLE web_scraping_request MODIFY `completed_handle` ENUM('HANDLE_NULL', 'HANDLE_EXTRACT_PRODUCTS');

~~~

2. "manifest.json" does not exist. It causes digital ocean's cache. If build manually without cache, this problem will fixed.

## Running Crawler Agent

1. Install node dependencies

~~~
cd firebase/functions && npm install
~~~

2. Start emulator

~~~
5001 port is default firebase functions port for localhost emulator

http://127.0.0.1:5001/functions

cd firebase && firebase emulators:start --only functions
~~~

3. Deploy firebase functions

~~~
 firebase login
 
 firebase deploy --only functions
~~~

## Deploying on Digital Ocean

To deploy application via digital ocean, this is your app spec file.

~~~
 TODO : add app spec file
~~~


# Symfony Web Application Boilerplate

To run your app, follow steps below.

1. Create Database
2. Configure Application
3. Build & Release Application
4. Run Application

## Build Phase: Localhost

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

# Build & Deploy firebase cloud functions

## Select firebase project

# Login to firebase.
firebase login

# Select firebase account to publish this app's functions.
firebase login:use meehouofficial@gmail.com

# Select firebase project
firebase use meehou-6cbe0

# Deploy & release firebase functions
firebase deploy --only functions

cd assets/cloud_functions && npm install && npx @puppeteer/browsers install chrome@stable && cd -
~~~

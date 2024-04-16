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

# Install firebase's functions dependecies
cd firebase/functions && npm install
~~~

2. Build & Deploy Firebase Cloud Functions

This needed to use puppeteer replayer.

~~~
# 1. Login to firebase with your account
firebase login

# 3. Select firebase account to publish this app's functions.
firebase login:use youremail@yourdomain.com

# 4. Select firebase project
firebase use your-firebase-project-id

# 5. Deploy & release firebase functions
firebase deploy --only functions
~~~

To emulate firebase functions;

~~~
# Start emulators
firebase emulators:start

# Go to emulators panel
http://127.0.0.1:4000

# Copy function urls
http://127.0.0.1:5001/your-project-id/us-central1/pingPong

# Change environment variables
CLOUD_FUNCTIONS_PING_PONG_URL=your-ping-pong-function-url
CLOUD_FUNCTIONS_PUPPETEER_REPLAYER_URL=your-puppeteer-replayer-function-url
~~~


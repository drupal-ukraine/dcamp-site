#!/usr/bin/env bash

HOME="/var/www/drupalcampkyiv.org"

echo "Getting last repository update";

cd $HOME
git pull

echo "Building composer packages"
cd $HOME
composer install

echo "Importing config and updating database"
cd $HOME
./vendor/bin/drush cr
./vendor/bin/drush cim -y
./vendor/bin/drush updb -y



echo "Building theme"

cd $HOME/docroot/themes/custom/dckyiv
npm install
gulp build

echo "Building QR app"
cd $HOME/docroot/modules/custom/dckyiv_commerce/dckyiv-qr-scanner
npm install
npm run build

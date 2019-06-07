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
./vendor/bin/drush updb -y
./vendor/bin/drush cim -y


echo "Building theme"
cd $HOME/docroot/themes/custom/dckyiv
npm install
npm run production

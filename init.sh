#! /bin/bash

cp /picbox/apache2.conf /etc/apache2/sites-enabled/000-default.conf

#mkdir -p /picbox/files/upload
mkdir -p /picbox/thumbs
mkdir -p /picbox/highlights
#chown -R www-data:www-data /picbox/files/upload
chown -R www-data:www-data /picbox/thumbs
chown -R www-data:www-data /picbox/highlights

exec apachectl -e info -DFOREGROUND



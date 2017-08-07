#! /bin/bash

cp /picbox/apache2.conf /etc/apache2/sites-enabled/000-default.conf

if [[ $HTTP_AUTH = digest ]]; then
    <apache2_auth.conf >>/etc/apache2/sites-enabled/000-default.conf
fi

sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 100M/' /etc/php5/apache2/php.ini
sed -i 's/post_max_size = 8M/post_max_size = 100M/' /etc/php5/apache2/php.ini

#mkdir -p /picbox/files/upload
mkdir -p /picbox/thumbs
mkdir -p /picbox/highlights
#chown -R www-data:www-data /picbox/files/upload
chown -R www-data:www-data /picbox/thumbs
chown -R www-data:www-data /picbox/highlights

exec apachectl -e info -DFOREGROUND

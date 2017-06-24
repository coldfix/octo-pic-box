pic-box
=======

Simple web interface showing a directory-listing or thumbnail-gallery of the
files or images in the ``files/`` subdirectory. Users can upload files if the
folder has write-permissions for all.

An example can be seen at pix.coldfix.eu_.

.. _pix.coldfix.eu: https://pix.coldfix.eu

This repo is fully under the motto *mistakes of the past*, both in terms of
the pictures that might be revealed and the source code that I wrote.

Deployment
----------

It is recommended to deploy using ``docker`` or ``docker-compose``.

To run the application in a docker-container behind port 6060 type:

.. code-block:: bash

    docker build . -t picbox
    docker run -d --restart=always \
        -v `pwd`:/picbox -v `pwd`/files:/picbox/files \
        -p 6060:80 --name picbox picbox

The same, using ``docker-compose``:

.. code-block:: bash

    docker-compose up

In order to run the application on a subdomain, you will need to setup a proxy
forward. Example ``nginx`` configuration to show the site on ``pix``
subdomain:

.. code-block:: nginx

    server {
        listen      80;
        listen [::]:80;
        listen      443 ssl;
        listen [::]:443 ssl;
        server_name pix.example.com pix.example.org;
        access_log /var/log/nginx/access_pics.log;
        location / {
            proxy_pass                          http://localhost:6060;
            proxy_set_header X-Real-IP          $remote_addr;
            proxy_set_header Host               $host;
            proxy_set_header X-Forwarded-For    $proxy_add_x_forwarded_for;
            proxy_set_header Upgrade            $http_upgrade;
            proxy_set_header Connection         upgrade;
        }
    }

Upload
------

To enable uploading to a particular subfolder, make it writable by all:

.. code-block:: bash

    mkdir -p files/public
    chmod 777 files/public

It is recommended to set a quota on the size of the directory. This can be
achieved by mounting it in a limited size filesystem. See the script
``setup_quota.zsh`` for an example how to do this. It sets up filesystem
mounted from the file ``pic.box``. Example to set 5 gigabyte limit:

.. code-block:: bash

    sudo ./setup_quota.zsh 5G

Authentication
--------------

`Digest access authentication`_ can be enabled by running the container with
the environment argument ``HTTP_AUTH=digest``, e.g.:

.. code-block:: bash

    docker run -d --restart=always \
        -v `pwd`:/picbox -v `pwd`/files:/picbox/files \
        -e HTTP_AUTH=digest \
        -p 6060:80 --name picbox picbox

or edit the ``docker-compose.yml`` accordingly.

You must also provide a authentication file. A new one can be created by
executing:

.. code-block:: bash

    ./setup_auth.zsh

.. _Digest access authentication: https://en.wikipedia.org/wiki/Digest_access_authentication

**NOTE:** It is recommended to use this only through SSL, i.e. via an HTTPS
proxy pass!


Big TODOs
~~~~~~~~~

- big image size due to using debian base image + apache2 + php etc
- want to rewrite this using python, using something like flask/bottle/klein,
  improve encapsulation, simplify reducing image size
- finer-grade control over auth: individual folders / only for upload
- quota without root access
- upload-ability shouldn't be tied to chmod 777

Don't hesitate to send me a PR that implements any of those!

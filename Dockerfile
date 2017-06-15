FROM debian:8.8

COPY . /picbox
WORKDIR /picbox

# php5-gd for imagecreatefromXXX
ARG runtime_deps="apache2 libapache2-mod-php5 php5 php5-cgi php5-gd php5-pecl-http imagemagick"
ARG build_deps="wget php5-dev"

RUN apt-get update && \
    apt-get install -y $build_deps $runtime_deps && \
    wget https://github.com/Yelp/dumb-init/releases/download/v1.2.0/dumb-init_1.2.0_amd64.deb && \
    dpkg -i dumb-init_*.deb && \
    rm dumb-init_*.deb && \
    cp apache2.conf /etc/apache2/sites-enabled/000-default.conf && \
    cd /etc/php5/apache2/conf.d && \
    ln -s ../../mods-available/raphf.ini     40-raphf.ini && \
    ln -s ../../mods-available/propro.ini    45-propro.ini && \
    ln -s ../../mods-available/pecl-http.ini 50-pecl-http.ini


VOLUME /picbox/files
VOLUME /var/log/apache2
EXPOSE 80

ENTRYPOINT ["/usr/bin/dumb-init", "--"]
CMD ["/picbox/init.sh"]

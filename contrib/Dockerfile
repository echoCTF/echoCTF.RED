FROM buildpack-deps:buster-curl
LABEL maintainer="Echothrust Solutions <info@echothrust.com>"
LABEL description="echoCTF.RED all in one"


ENV DEBIAN_FRONTEND noninteractive
RUN set -ex \
    && apt-get update \
    && apt-get install --no-install-recommends -y build-essential gcc \
    autoconf automake git zip unzip \
    mariadb-server mariadb-client mcrypt memcached apache2 \
    libmemcached-dev libtool libmariadbclient-dev \
    php php-gd php-mbstring php-mysqli php-dom php-intl php-curl php-memcache \
    composer

WORKDIR /var/www/echoCTF.RED
COPY . .

WORKDIR /usr/src
RUN set -ex; \
    echo "[mysqld]\nevent_scheduler=on" >/etc/mysql/mariadb.conf.d/50-mysqld.cnf; \
    service mysql start; \
    cp /var/www/echoCTF.RED/contrib/apache2-red.conf /etc/apache2/sites-enabled/echoCTF.RED.conf; \
    git clone https://github.com/echoCTF/memcached_functions_mysql.git; \
    cd memcached_functions_mysql; \
    ./config/bootstrap; \
    ./configure --with-mysql=/usr/bin/mariadb_config; \
    make ; \
    cp src/.libs/libmemcached_functions_mysql.so /usr/lib/x86_64-linux-gnu/mariadb19/plugin/; \
    mysql mysql < sql/install_functions.sql; \
    cd /var/www/echoCTF.RED; \
    mysqladmin create echoCTF; \
    mysql echoCTF<schemas/echoCTF.sql; \
    mysql echoCTF<schemas/echoCTF-routines.sql; \
    mysql echoCTF<schemas/echoCTF-triggers.sql; \
    mysql echoCTF<schemas/echoCTF-events.sql; \
    cp backend/config/cache-local.php backend/config/cache.php; \
    cp backend/config/validationKey-local.php backend/config/validationKey.php; \
    cp backend/config/db-sample.php backend/config/db.php; \
    cp frontend/config/memcached-local.php frontend/config/memcached.php; \
    cp frontend/config/validationKey-local.php frontend/config/validationKey.php; \
    cp frontend/config/db-local.php frontend/config/db.php; \
    mkdir -p backend/web/assets frontend/web/assets; \
    chown www-data backend/web/assets frontend/web/assets; \
    chown www-data backend/runtime frontend/runtime; \
    cd backend; composer install; \
    cd ../frontend; composer install; \
    cd ..; \
    ./backend/yii migrate --interactive=0; \
    ./backend/yii init_data --interactive=0; \
    ./backend/yii ssl/create-ca; \
#    ./backend/yii sysconfig/set mail_from dontreply@echoctf.red; \
#    ./backend/yii user/create echothrust info@echothrust.com echothrust; \
#    ./backend/yii player/register echothrust info@echothrust.com echothrust echothrust offense 1;\
    a2enmod rewrite; \
    mysql -e "update user set plugin='mysql_native_password' where user='root'" mysql; \
    echo "init_file=/etc/mysql/mysql-init.sql" >>/etc/mysql/mariadb.conf.d/50-mysqld.cnf; \
    sed -e "s/^-- #//g" contrib/mysql-init.sql >/etc/mysql/mysql-init.sql; \
    service mysql stop

ADD contrib/entrypoint.sh /
RUN chmod 0700 /entrypoint.sh

RUN rm -rf /usr/src/* /var/lib/apt/lists/*

WORKDIR /var/www/echoCTF.RED
CMD ["tail -f /var/log/apache2/error.log"]
ENTRYPOINT ["/entrypoint.sh"]

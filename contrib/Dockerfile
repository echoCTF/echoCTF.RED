FROM buildpack-deps:bullseye-curl
LABEL maintainer="Echothrust Solutions <info@echothrust.com>"
LABEL description="echoCTF.RED all in one"
ARG VPNUSER=vpnuser
ARG VPNUSERPASS=vpnuserpass
ARG GITHUB_OAUTH_TOKEN

ENV DEBIAN_FRONTEND noninteractive
RUN set -ex \
    && apt-get update \
    && apt-get install --no-install-recommends -y build-essential gcc tini \
    autoconf automake git zip unzip \
    mariadb-server mariadb-client mcrypt memcached apache2 \
    libmemcached-dev libtool libmariadb-dev \
    php php-gd php-mbstring php-mysqli php-dom php-intl php-curl php-memcache \
    composer vim openvpn

WORKDIR /var/www/echoCTF.RED
COPY . .

WORKDIR /usr/src
RUN set -ex; \
    echo "[mysqld]\nevent_scheduler=on" >/etc/mysql/mariadb.conf.d/50-mysqld.cnf; \
    sed -i -E 's/bind-address.*$//g' /etc/mysql/mariadb.conf.d/50-server.cnf; \
    echo "plugin_load_add = ha_federatedx">> /etc/mysql/mariadb.conf.d/50-server.cnf; \
    echo "plugin_load_add = ha_blackhole">> /etc/mysql/mariadb.conf.d/50-server.cnf; \
    echo "blackhole = FORCE">> /etc/mysql/mariadb.conf.d/50-server.cnf; \
    sed -i -e 's/127.0.0.1/0.0.0.0/g' /etc/memcached.conf; \
    service mariadb start; \
    cp /var/www/echoCTF.RED/contrib/apache2-red.conf /etc/apache2/sites-enabled/echoCTF.RED.conf; \
    git clone https://github.com/echoCTF/memcached_functions_mysql.git; \
    cd memcached_functions_mysql; \
    ./config/bootstrap; \
    ./configure --with-mysql=/usr/bin/mariadb_config; \
    make ; \
    cp src/.libs/libmemcached_functions_mysql.so /usr/lib/mysql/plugin/; \
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
    cp frontend/config/memcached-local.php frontend/config/cache.php; \
    cp frontend/config/validationKey-local.php frontend/config/validationKey.php; \
    cp frontend/config/db-local.php frontend/config/db.php; \
    mkdir -p backend/web/assets frontend/web/assets backend/runtime frontend/runtime; \
    chown www-data backend/web/assets frontend/web/assets; \
    chown www-data backend/runtime frontend/runtime; \
#    [ -z "${GITHUB_OAUTH_TOKEN}" ] || composer config -g github-oauth.github.com "${GITHUB_OAUTH_TOKEN}"; \
#    [ -z "${GITHUB_OAUTH_TOKEN}" ] || cd backend; composer install --no-dev --prefer-dist --no-progress --no-suggest; \
#    [ -z "${GITHUB_OAUTH_TOKEN}" ] || cd ../frontend; composer install --no-dev --prefer-dist --no-progress --no-suggest; \
#    [ -z "${GITHUB_OAUTH_TOKEN}" ] || cd ..; \
    ./backend/yii migrate --interactive=0; \
    ./backend/yii init_data --interactive=0; \
    a2enmod rewrite; \
    mysql -e "CREATE USER '$VPNUSER'@'%' IDENTIFIED BY '$VPNUSERPASS'" mysql;\
    mysql -e "GRANT ALL PRIVILEGES ON echoCTF.* TO '$VPNUSER'@'%'" mysql;\
    echo "init_file=/etc/mysql/mysql-init.sql" >>/etc/mysql/mariadb.conf.d/50-mysqld.cnf; \
    echo "CALL echoCTF.init_mysql();"  >/etc/mysql/mysql-init.sql; \
    service mariadb stop; \
    rm -rf /root/.composer /usr/src/* /var/lib/apt/lists/*


ADD contrib/entrypoint.sh /
RUN chmod 0700 /entrypoint.sh

RUN rm -rf /usr/src/* /var/lib/apt/lists/*

EXPOSE 8080/tcp
EXPOSE 8081/tcp
EXPOSE 3306/tcp
EXPOSE 11211/tcp

WORKDIR /var/www/echoCTF.RED
CMD ["tail -f /var/log/apache2/error.log"]
ENTRYPOINT ["tini", "--", "/entrypoint.sh"]

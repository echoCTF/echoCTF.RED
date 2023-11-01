# echoCTF.RED Installation Instructions for Linux

These instructions will guide you in installing the web interfaces on any linux based on Debian.

## Install from source
The guide assumes Debian 10 (buster).

### Install the needed packages
```sh
apt-get update
apt-get install build-essential gcc git mariadb-server mariadb-client mcrypt \
memcached libmemcached-dev apache2 libtool libmariadb-dev autoconf \
automake php composer php-gd php-mbstring php-mysqli php-dom php-intl \
php-curl php-memcached
```

### Enable event scheduler and blackhole plugin on the database
```sh
echo -e "[mysqld]\nevent_scheduler=on\n" >/etc/mysql/mariadb.conf.d/50-mysqld.cnf
echo "plugin_load_add = ha_blackhole" >>/etc/mysql/mariadb.conf.d/50-mysqld.cnf
```

### Start the services
```sh
service memcached restart
service mysql restart
```

### Clone the needed repositories
```sh
cd /var/www
git clone --depth 1 https://github.com/echoCTF/memcached_functions_mysql.git
git clone --depth 1 https://github.com/echoCTF/MySQL-global-user-variables-UDF.git
git clone --depth 1 https://github.com/echoCTF/echoCTF.RED.git
```

### Build the memcached udf
```sh
cd /var/www/memcached_functions_mysql
./config/bootstrap
./configure --with-mysql=/usr/bin/mariadb_config
make
cp src/.libs/libmemcached_functions_mysql.so /usr/lib/x86_64-linux-gnu/mariadb19/plugin/
mysql mysql < sql/install_functions.sql
```

### Build the global user variables UDF
```sh
cd MySQL-global-user-variables-UDF
make DESTDIR=/usr/lib/x86_64-linux-gnu/mariadb19/plugin/ all install
mysql mysql < global_user_variables.sql
```

### Import the database timezones information
```sh
mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql mysql
```

### Prepare the database
```sh
cd /var/www/echoCTF.RED
mysql -e "CREATE DATABASE echoCTF CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
mysql echoCTF<schemas/echoCTF.sql
mysql echoCTF<schemas/echoCTF-routines.sql
mysql echoCTF<schemas/echoCTF-triggers.sql
mysql echoCTF<schemas/echoCTF-events.sql
cp contrib/mysql-init.sql /etc/mysql/mysql-init.sql
```

**NOTE:** If you change the name of the database make sure you also update the file `/etc/mysql/mysql-init.sql` to reflect this change.

### Copy the sample configuration files and update to reflect your system
```sh
cp backend/config/cache-local.php backend/config/cache.php
cp backend/config/validationKey-local.php backend/config/validationKey.php
cp backend/config/db-sample.php backend/config/db.php
cp frontend/config/memcached-local.php frontend/config/cache.php
cp frontend/config/validationKey-local.php frontend/config/validationKey.php
cp frontend/config/db-local.php frontend/config/db.php
```

**NOTE:** If you keep the default db.php on the applications, update the mysql authentication plugin to allow root access
```sh
mysql -e "ALTER USER root@localhost IDENTIFIED VIA mysql_native_password" mysql
mysql -e "SET PASSWORD = PASSWORD('')" mysql
```

For older versions of MariaDB < 10.4.3, you can try the following instead
```sh
mysql -e "update user set plugin='mysql_native_password' where user='root'" mysql
```

### Create and update permissions for folders needed by the applications
```sh
mkdir -p backend/web/assets frontend/web/assets
chown www-data backend/web/assets frontend/web/assets
chown www-data backend/runtime frontend/runtime
```

### Install composer files
```sh
composer install --no-dev --prefer-dist --no-progress --no-suggest -d backend
composer install --no-dev --prefer-dist --no-progress --no-suggest -d frontend
```

### Run migrations and import initial data
```sh
cd /var/www/echoCTF.RED
./backend/yii migrate --interactive=0
./backend/yii init_data --interactive=0
./backend/yii migrate-sales --interactive=0
./backend/yii template/emails
```

### Create an admin user for the backend
```sh
./backend/yii user/create username email password
```

### Create the CA keys for signing user certificates
Edit the file `backend/config/CA.cnf`, `frontend/config/CA.cnf`,
`backend/config/params.php` and `frontend/config/params.php`to include your own
details for the CA and generated certificates used by OpenVPN.

Once this is done create your certification authority keys the create-ca command.
```sh
./backend/yii ssl/create-ca
```

### Set the mail from Sysconfig key
```sh
./backend/yii sysconfig/set mail_from dontreply@example.red
```

Note that in order to allow registrations from the web interface you need to
also set the following sysconfig keys
```sh
./backend/yii sysconfig/set mail_fromName "Mail From Name"
./backend/yii sysconfig/set mail_host smtp.host.com
./backend/yii sysconfig/set mail_port 25
```

### Register an active user from the command line
```sh
./backend/yii player/register username email fullname password offense 1
```

### Prepare the webserver

Copy the sample apache2 config and update to reflect your settings.
```sh
a2enmod rewrite
cp contrib/apache2-red.conf /etc/apache2/sites-enabled/echoctf.conf
service apache2 restart
```

The default interfaces are accessible under [http://localhost:8080/](http://localhost:8080/) for the frontend and [http://localhost:8081/](http://localhost:8081/) for the backend.

### Make mysql populate memcache on reboot and service restarts
```sh
echo "init_file=/etc/mysql/mysql-init.sql" >>/etc/mysql/mariadb.conf.d/50-mysqld.cnf
mysql < /etc/mysql/mysql-init.sql
```
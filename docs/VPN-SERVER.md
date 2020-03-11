# VPN Server Installation

The VPN server for the platform is the system that allows participants to
connect to the target infrastructure as well as keeping track of the findings.

The following guide covers the installation of the needed application on an
OpenBSD to act as a VPN gateway.


Install the needed packages
```sh
pkg_add -vi curl git openvpn-2.4.7p1 mariadb-server easy-rsa \
 libmemcached libtool autoconf-2.69p2 automake-1.16.1 \
 composer php-gd-7.3.15 php-curl-7.3.15 php-intl-7.3.15 php-pdo_mysql-7.3.15 \
 php-zip-7.3.15 php-mcrypt-7.3.15 pecl73-memcached
```

Enable the installed php modules
```sh
ln -s  /etc/php-7.3.sample/* /etc/php-7.3/
```

Start the mysql server
```sh
mysql_install_db
echo "event_scheduler=on" >>/etc/my.cnf
rcctl restart mysqld
```

Clone the needed repos
```sh
git clone --depth 1 https://github.com/echoCTF/memcached_functions_mysql.git
git clone --depth 1 https://github.com/echoCTF/findingsd.git
git clone --depth 1 https://github.com/echoCTF/echoCTF.RED.git
```

Build and install `memcached_functions_mysql`
```sh
export AUTOMAKE_VERSION=1.16 AUTOCONF_VERSION=2.69
./configure --with-libmemcached=/usr/local
make
cp src/.libs/libmemcached_functions_mysql.so.0.0 /usr/local/lib/mysql/plugin/
mysql mysql < sql/install_functions.sql
```

Build and install `findingsd`
```sh
cd ../findingsd
make
install -c -s -o root -g bin -m 555 findingsd /usr/local/sbin/findingsd
install -c -o root -g wheel -m 555 findingsd.rc /etc/rc.d/findingsd
echo up>/etc/hostname.pflog1
rcctl set findingsd status on
rcctl set findingsd flags -l pflog1 -n echoCTF -u root
useradd -d /var/empty _findingsd
```

Configure the backend
```sh
cd echoCTF.RED
cp backend/config/cache-local.php backend/config/cache.php
cp backend/config/validationKey-local.php backend/config/validationKey.php
cp backend/config/db-sample.php backend/config/db.php
cd backend && composer install
```

Edit `backend/config/db.php` and modify the database host, username and
password. The backend needs to be able to connect to the host running the database
for your installation.

If you **run all the components** (vpn, frontend, backend, mysql, memcached) on the
same host import the `echoCTF.RED/contrib/findingsd.sql`
```sh
mysql echoCTF < /tmp/findingsd.sql
```

If the VPN host **runs on a different host** than your main database server
edit the file `echoCTF.RED/contrib/findingsd-federated.sql` and replace the
following strings to their corresponding value. For our example we will use
* `{{db.user}}` database username (ex `vpnuser`)
* `{{db.pass}}` database user password (ex `vpnuserpass`)
* `{{db.host}}` database host (prefer IP ex `10.7.0.20`)
* `{{db.name}}` database name (default ex `echoCTF`)

```sh
sed -e 's#{{db.host}}#10.7.0.20#g' \
-e 's#{{db.user}}#vpnuser#g' \
-e 's#{{db.pass}}#vpnuserpass#g' \
-e 's#{{db.name}}#echoCTF#g' echoCTF/contrib/findingsd-federated.sql > /tmp/findingsd.sql
mysqladmin create echoCTF
mysql echoCTF < /tmp/findingsd.sql
```

Prepare `/etc/sysctl.conf`
```sh
echo "net.inet.ip.forwarding=1" >> /etc/sysctl.conf
```

Create the needed vpn server certificates and keys
```sh
./backend/yii ssl/get-ca 1
./backend/yii ssl/create-cert "VPN Server"
```

Create the OpenVPN needed structure
```sh
mkdir -p /etc/openvpn/certs /etc/openvpn/client_confs /var/log/openvpn /etc/openvpn/crl
install -d -m 700 /etc/openvpn/private
```

# VPN Server Installation

The VPN server for the platform is the system that allows participants to
connect to the target infrastructure as well as keeping track of the findings.

The following guide covers the installation of the needed applications on
OpenBSD 6.6 to act as a VPN gateway.

The following network details will be used throughout this guide
* vpn server egress interface: `em0`
* vpn server egress address: `172.16.10.109`
* vpn server dmz interface: `em1`
* vpn server dmz address: `10.0.0.254/16`
* vpn server tun0 address: `10.10.0.1`
* vpn server assigned range: `10.10.0.0/16`
* targets network: `10.0.100.0/16`
* mysql/memcache server: `172.16.0.1`

Install the needed packages
```sh
pkg_add -vi curl git openvpn-2.4.7p1 mariadb-server easy-rsa \
 libmemcached libtool autoconf-2.69p2 automake-1.16.1 \
 composer php-gd-7.3.15 php-curl-7.3.15 php-intl-7.3.15 php-pdo_mysql-7.3.15 \
 php-zip-7.3.15 pecl73-memcached
# php-mcrypt-7.3.15
```

Prepare and start the mysql server
```sh
mysql_install_db
rcctl set mysqld status on
echo "event_scheduler=on" >>/etc/my.cnf
echo "plugin_load_add = ha_federatedx">>/etc/my.cnf
rcctl restart mysqld
```

Enable the installed php modules
```sh
ln -s  /etc/php-7.3.sample/* /etc/php-7.3/
```

Clone the needed repos
```sh
git clone --depth 1 https://github.com/echoCTF/memcached_functions_mysql.git
git clone --depth 1 https://github.com/echoCTF/findingsd.git
git clone --depth 1 https://github.com/echoCTF/echoCTF.RED.git
```

Build and install `memcached_functions_mysql`
```sh
cd memcached_functions_mysql
export AUTOMAKE_VERSION=1.16 AUTOCONF_VERSION=2.69
./config/bootstrap
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
sh /etc/netstart pflog1
rcctl set findingsd status on
rcctl set findingsd flags -l pflog1 -n echoCTF -u root
useradd -d /var/empty _findingsd
```

Configure the backend
```sh
cd ../echoCTF.RED
cp backend/config/cache-local.php backend/config/cache.php
cp backend/config/validationKey-local.php backend/config/validationKey.php
cp backend/config/db-sample.php backend/config/db.php
```

Edit `backend/config/db.php` and modify the database host, username and
password. The backend needs to be able to connect to the host running the database
for your installation.

Run composer from `backend/`
```sh
cd backend && composer install && cd ..
```

If you **run all the components** (vpn, frontend, backend, mysql, memcached) on the
same host import the `echoCTF.RED/contrib/findingsd.sql`
```sh
mysql echoCTF < contrib/findingsd.sql
```

If the VPN host **runs on a different host** than your main database server
edit the file `echoCTF.RED/contrib/findingsd-federated.sql` and replace the
following strings to their corresponding value. For our example we will use
* `{{db.user}}` database username (ex `vpnuser`)
* `{{db.pass}}` database user password (ex `vpnuserpass`)
* `{{db.host}}` database host (prefer IP ex `172.16.0.1`)
* `{{db.name}}` database name (default ex `echoCTF`)

```sh
sed -e 's#{{db.host}}#172.16.0.1#g' \
-e 's#{{db.user}}#vpnuser#g' \
-e 's#{{db.pass}}#vpnuserpass#g' \
-e 's#{{db.name}}#echoCTF#g' contrib/findingsd-federated.sql > /tmp/findingsd.sql
mysqladmin create echoCTF
mysql echoCTF < /tmp/findingsd.sql
```

Prepare `/etc/sysctl.conf`
```sh
echo "net.inet.ip.forwarding=1" >> /etc/sysctl.conf
sysctl net.inet.ip.forwarding=1
```

Create the OpenVPN needed structure
```sh
mkdir -p /etc/openvpn/certs /etc/openvpn/client_confs /var/log/openvpn /etc/openvpn/crl /etc/openvpn/ccd
install -d -m 700 /etc/openvpn/private
```

Copy the server configuration and script
```sh
cp contrib/openvpn_tun0.conf /etc/openvpn
install -m 555 -o root contrib/echoctf_updown_mysql.sh /etc/openvpn
```

Edit `/etc/openvpn/openvpn_tun0.conf` and uncomment the first line and replace
`A.B.C.D` with the system egress IP.

Edit the script at `/etc/openvpn/echoctf_updown_mysql.sh` and update the first
line with the IP of your database server. If all services run on the local
system use `127.0.0.1` alternatively use the same IP we used on the findingsd.sql examples above (172.16.0.1)
```sh
sed -i -e 's#{{db.host}}#172.16.0.1#g' /etc/openvpn/echoctf_updown_mysql.sh
```

Prepare the tun0 interface and rc scripts
```
echo "up" >/etc/hostname.tun0
echo "group offense">>/etc/hostname.tun0
rcctl set openvpn status on
rcctl set openvpn flags --dev tun0 --config /etc/openvpn/openvpn_tun0.conf
sh /etc/netstart tun0
```

Create the needed vpn server certificates and keys
```sh
cp contrib/crl_openssl.conf /etc/openvpn/crl/
touch /etc/openvpn/crl/index.txt
echo "00" > /etc/openvpn/crl/number
echo "OPENVPN_ADMIN_PASSWORD">/etc/openvpn/private/mgmt.pwd
./backend/yii ssl/get-ca 1
./backend/yii ssl/create-cert "VPN Server"
mv echoCTF-OVPN-CA.crt /etc/openvpn/private/echoCTF-OVPN-CA.crt
mv echoCTF-OVPN-CA.key /etc/openvpn/private/echoCTF-OVPN-CA.key
mv VPN\ Server.crt /etc/openvpn/private/VPN\ Server.crt
mv VPN\ Server.key /etc/openvpn/private/VPN\ Server.key
chmod 400 /etc/openvpn/private/*
openssl dhparam -out /etc/openvpn/dh.pem 4096
openvpn --genkey --secret /etc/openvpn/private/vpn-ta.key
# USE THIS IF ssl/create-crl fails
#openssl ca -gencrl -keyfile /etc/openvpn/private/echoCTF-OVPN-CA.key -cert /etc/openvpn/private/echoCTF-OVPN-CA.crt -out /etc/openvpn/crl.pem -config /etc/openvpn/crl/crl_openssl.conf
./backend/yii ssl/create-crl
./backend/yii ssl/load-vpn-ta
```

Prepare pf
```sh
touch /etc/maintenance.conf /etc/targets.conf /etc/match-findings-pf.conf
cp contrib/pf-vpn.conf /etc/pf.conf
./backend/yii target/pf
```

Edit `/etc/pf.conf` and replace the address from `Line:11` for table `moderators_allowed`. The current one `0.0.0.0/0` allows everyone to access all the services. Once done verify the the validity of `pf.conf` and load it
```sh
pfctl -nvf /etc/pf.conf
pfctl -f /etc/pf.conf
```

Start the services and test out
```sh
rcctl start findingsd
rcctl start openvpn
```

Update your cron to include the following (assuming you cloned the repositories under `/root`)
```
# check target container health status and spin requests
*/2	*	*	*	*	/root/echoCTF.RED/backend/yii target/healthcheck 1
# Perform scheduled powerup/powerdown of targets based on scheduled_at
*/4	*	*	*	*	/root/echoCTF.RED/backend/yii target/cron
# Restart containers every 24 hours to ensure clean state
*/10	*	*	*	*	/root/echoCTF.RED/backend/yii target/restart
# Generate CRL with revoked player certificates
@midnight /root/echoCTF.RED/backend/yii ssl/generate-crl
```

Finally ensure to set the `vpngw` sysconfig key to the IP that the participants will connect to openvpn.
```sh
./backend/yii sysconfig/set vpngw 172.16.10.109
```

Ensure that your `em1` interface is assigned `group dmz`
```sh
echo "group dmz">>/etc/hostname.em1
```

Restart the system and you should be up and running.

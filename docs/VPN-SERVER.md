# VPN Server Installation

The VPN server for the platform is the system that allows participants to
connect to the target infrastructure as well as keeping track of the findings.

The following guide covers the installation of the needed applications on
OpenBSD 6.6 to act as a VPN gateway.

<img src="https://raw.githubusercontent.com/echoCTF/echoCTF.RED/master/docs/assets/docker-compose-novpn-topology.png?nocache" alt="echoCTF.RED docker-compose topology" width="400px"/>

Before you start ensure you have the db server up and running as the VPN needs
to connect to the database server to operate. Check the [DOCKER-COMPOSE-NOVPN.md](DOCKER-COMPOSE-NOVPN.md)


The following network details will be used throughout this guide

* vpn server egress interface: `em0`
* vpn server egress address: `172.26.0.1`
* vpn server targets interface: `em1`
* vpn server targets address: `10.0.160.254/24`
* vpn server tun0 address: `10.10.0.1`
* vpn server assigned range: `10.10.0.0/16`
* targets network: `10.0.100.0/16`
* mysql/memcache server: `172.24.0.253`

There is an experimental playbook you can run locally on your OpenBSD that will
configure all that is needed for you.

Before you start ensure you are able to access your existing database from the
server by running something like the following
```sh
nc -zv 172.24.0.253 3306
nc -zv 172.24.0.253 11211
```

**NOTE:** If you're using the supplied `docker-compose` without VPN this IP will be
the IP of the docker host and not the container.

```sh
pkg_add -vvi git ansible
git clone https://github.com/echoCTF/echoCTF.RED.git
cd echoCTF.RED/ansible
ansible-playbook runonce/vpngw.yml
```

Once you answer the questions asked you are set to go. Restart the system and
once it comes back up following the instructions at
[After restart](#after-restart) and you should be up and running.


## Manual Installation
Or if you'd rather execute the playbook in a non interactive mode, copy the
file `templates/default-settings.yml` and edit to with your own values.
```sh
cp templates/default-settings.yml settings.yml
ansible-playbook runonce/vpngw.yml -e '@settings.yml'
```

Alternatively you can manually configure your system by following these steps,
adapting them to your needs where needed.

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
echo "wait_timeout = 2880000">>/etc/my.cnf
echo "interactive_timeout = 2880000">>/etc/my.cnf
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
cd backend
composer install --no-dev --prefer-dist --no-progress --no-suggest
cd ..
```

If you **run all the components** (vpn, frontend, backend, mysql, memcached) on the
same host import the `echoCTF.RED/contrib/findingsd.sql`
```sh
mysql echoCTF < contrib/findingsd.sql
```

If the VPN host **runs on a different host** than your main database server
edit the file `echoCTF.RED/contrib/findingsd-federated.sql` and replace the
following strings to their corresponding value. For our example we will use
the following details

* `{{db_user}}` database username (ex `vpnuser`)
* `{{db_pass}}` database user password (ex `vpnuserpass`)
* `{{db_host}}` database host (prefer IP ex `172.24.0.253`)
* `{{db_name}}` database name (default ex `echoCTF`)

**NOTE:** If you are running the docker container that we provide then a user
already exists on the database with the following credentials, otherwise you'll
have to GRANT the permissions to your mysql host.

* mysql user: `vpnuser`
* mysql password: `vpnuserpass`


```sh
sed -e 's#{{db_host}}#172.24.0.253#g' \
-e 's#{{db_user}}#vpnuser#g' \
-e 's#{{db_pass}}#vpnuserpass#g' \
-e 's#{{db_name}}#echoCTF#g' contrib/findingsd-federated.sql > /tmp/findingsd.sql
mysql -e "CREATE DATABASE echoCTF CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
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
system use `127.0.0.1` alternatively use the same IP we used on the findingsd.sql examples above (172.24.0.253)
```sh
sed -i -e 's#{{db.host}}#172.24.0.253#g' /etc/openvpn/echoctf_updown_mysql.sh
```

Prepare the tun0 interface and rc scripts
```sh
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
./backend/yii migrate --interactive=0
./backend/yii init_data --interactive=0
./backend/yii migrate-sales --interactive=0
./backend/yii template/emails --interactive=0
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
cp ansible/templates/pf.conf.j2 /etc/pf.conf
cp ansible/templates/vpn.service.conf.j2 /etc/service.pf.conf
touch /etc/administrators.conf /etc/maintenance.conf /etc/moderators.conf
touch /etc/registry_clients.conf /etc/registry_servers.conf /etc/targets.conf
./backend/yii cron/pf
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

Update your cron to include the following (assuming you cloned the repositories under `/root`) and make sure you update your PATH variable
```
  PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin

  # check target container health status and spin requests
  */2 * * * * /root/echoCTF.RED/backend/yii target/healthcheck 1

  # Perform scheduled powerup/powerdown of targets based on scheduled_at
  */4 * * * * /root/echoCTF.RED/backend/yii cron

  # Restart containers every 24 hours to ensure clean state
  */10 * * * * /root/echoCTF.RED/backend/yii target/restart

  # Generate CRL with revoked player certificates
  @midnight /root/echoCTF.RED/backend/yii ssl/generate-crl
```

Finally ensure to set the `vpngw` sysconfig key to the IP that the participants will connect to openvpn.
```sh
./backend/yii sysconfig/set vpngw 172.26.0.1
```

Also set the PF tag for the firewall/findings rules generation
```sh
./backend/yii sysconfig/set offense_registered_tag OFFENSE_REGISTERED
```

Ensure that your `em1` interface is assigned `group targets`
```sh
echo "group targets">>/etc/hostname.em1
```

Restart the system and you should be up and running.


## After restart

Set the mail FROM system configuration key
```sh
./backend/yii sysconfig/set mail_from dontreply@example.red
```

Note that in order to allow registrations from the web interface you need to
also set the following sysconfig keys
```sh
./backend/yii sysconfig/set mail_fromName	"Mail From Name"
./backend/yii sysconfig/set mail_host smtp.host.com
./backend/yii sysconfig/set mail_port 25
```

Create a backend user and a frontend user by executing the following commands
```sh
# backend user
./backend/yii user/create username email password
# frontend player
./backend/yii player/register "username" "email" "fullname" "password" offense 1
```

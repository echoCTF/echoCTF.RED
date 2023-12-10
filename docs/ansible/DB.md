# Database playbook (ansible/runonce/db.yml)

The playbook can run be run in remote and local mode depending on your setup
and access.

**REMOTE**
Connect with SSH as root and ask password to connect
```sh
ansible-playbook runonce/db.yml -i 192.168.1.12, -uroot -k
```

**LOCAL**
Run on local OpenBSD system. Current user `root`.
```sh
pkg_add -vvi ansible
ansible-playbook runonce/db.yml --connection=local -i 127.0.0.1,
```

You can additionally provide a settings file (see `templates/default-settings.yml`)
```sh
ansible-playbook runonce/db.yml -e @settings.yml --connection=local -i 127.0.0.1,
```

## Playbook Tasks

* Installs needed packages
* Creates Users:
  * sysadmin / CTF Admin / uid: 375
* Creates the following `mysql_users`:
  * name: `participantUI`, password: `participantUI`, host:  {{pui_ip}}
  * name: `moderatorUI`, password: `moderatorUI`, host: {{mui_ip}}
  * name: `moderatorUI`, password: `moderatorUI`, host: {{vpn_ip}}
  * name: `vpnuser`, password: `vpnuserpass`, host: {{vpn_ip}}
  * name: `participantUI`, password: `participantUI`, host: localhost
  * name: `moderatorUI`, password: `moderatorUI`, host: localhost
  * name: `participantUI`, password: `participantUI`, host: 127.0.0.1
  * name: `moderatorUI`, password: `moderatorUI`, host: 127.0.0.1
* Fetch and add the github ssh keys from the defined `sshkeys` users
* Configures the `/etc/sysctl.conf`:
* Performs the following `rcctl` operations on the following services:
  * `check_quotas` => `disable`
  * `cron` => `disable`
  * `smtpd` => `disable`
  * `pflogd` => `disable`
  * `slaacd` => `disable`
  * `sndiod` => `disable`
  * `ntpd` => `enable`
  * `mysqld` => `enable`
  * `memcached` => `enable`
* Configures `/etc/my.cnf`:
* Allow sysadmin to execute 'doas'
* Configure `PS1`, `HIISTFILE` & `HISTSIZE` on root and skeleton
* Set authorized keys for root & sysadmin
* Clone sources repo if remote execution
* Bootstrap and start mysql
* Clone configure and install `memcached_functions_mysql` repo
* Clone configure and install `MySQL-global-user-variables-UDF` repo
* Create mysql db schema
* Import mysql schema
* Copy `mysql-init.sql` for populating memcached at boot
* Configures and enables `memcached`
* Executes `fw_update`
* Executes `syspatch`

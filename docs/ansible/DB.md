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


## Playbook Tasks
* Installs the packages:
  - curl
  - git
  - mariadb-server
  - memcached-1.5.18
  - libmemcached
  - py3-mysqlclient
  - libtool
  - autoconf-2.69p2
  - automake-1.16.2

* Create Users:
  * sysadmin, comment: CTF Admin, uid: 375, password: *

* Creates the following mysql_users:
  - name: participantUI, password: 'participantUI', host:  {{pui_ip}}
  - name: moderatorUI, password: 'moderatorUI', host: {{mui_ip}}
  - name: moderatorUI, password: 'moderatorUI', host: {{vpn_ip}}
  - name: vpnuser, password: 'vpnuserpass', host: {{vpn_ip}}
  - name: participantUI, password: 'participantUI', host: localhost
  - name: moderatorUI, password: 'moderatorUI', host: localhost
  - name: participantUI, password: 'participantUI', host: 127.0.0.1
  - name: moderatorUI, password: 'moderatorUI', host: 127.0.0.1

* Fetch and add the github ssh keys from the defined `sshkeys` users

* Configures the `/etc/sysctl.conf`:
```
  kern.bufcachepercent: 30
  net.inet.ip.ifq.maxlen: 2560
  net.inet.ip.maxqueue: 2048
  kern.somaxconn: 2048
  net.bpf.bufsize: 2097152
  net.bpf.maxbufsize: 4194304
  kern.seminfo.semmni: 1024
  kern.seminfo.semmns: 4096
  kern.shminfo.shmmax: 67018864
  kern.shminfo.shmall: 32768
  kern.maxfiles: 104060
```

* Performs the following `rcctl` operations:
  - name: check_quotas, state: "disable"
  - name: cron, state: "disable"
  - name: ntpd, state: "enable"
  - name: pflogd, state: "disable"
  - name: slaacd, state: "disable"
  - name: smtpd, state: "disable"
  - name: sndiod, state: "disable"
  - name: memcached, state: "enable"
  - name: mysqld, state: "enable"

* Configures `/etc/my.cnf`:
  - init_file:  "/etc/mysql-init.sql"
  - bind-address:  "{{db_ip}}"
  - character-set-server: utf8
  - collation-server: utf8_unicode_ci
  - default-storage-engine: innodb
  - default-time-zone: "'+00:00'"
  - event_scheduler: "ON"
  - skip-character-set-client-handshake: 1
  - skip-external-locking: 1
  - skip-name-resolve: 1
  - plugin_load_add: "ha_federatedx"
  - plugin_load_add: "ha_blackhole"
  - blackhole: "FORCE"

* Allow sysadmin to execute 'doas'
* Configure PS1, HIISTFILE & HISTSIZE on root and skeleton
* Set authorized keys for root & sysadmin
* Clone sources repo if remote execution
* Bootstrap and start mysql
* Create mysql db schema
* Clone configure and install memcached_functions_mysql repo
* Import mysql schema
* Copy mysql-init.sql for populating memcached at boot
* configure and memcached
* Execute fw_update
* Execute syspatch

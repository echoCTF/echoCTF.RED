# The PF
For the standalone installations on OpenBSD, there is also a set of PF
configuration files being supplied. The following document describes what are
the existing rules and how they are meant to be used.

You are free to change or adapt the rulesets as you please, however the
existing configuration provides a good foundation to build upon.

All the hosts have the same base `pf.conf` with same tables and rules and
include portions of PF that reflect to them from the file `/etc/services.pf.conf`.

## General policy
1. Skip filtering on `lo` interface group. This means `lo0` and any other `lo*` interface that has the lo group.
2. Scrub all packets clearing DF and setting the max-mss to 1440
3. Packets leaving our egress interface get nated to the first IP assigned to it
4. Packets on tun interface that their source IP can be found on `offense_activated` network is tagged as `OFFENSE_REGISTERED`
5. Block quick and return an answer for packet to 239.255.255.250, 224/8.
6. Block and log droped packets and return a reply
7. Block quick and drop packets from `banned`
8. Pass quick from `administrators`
9. Pass quick on traffic originating from the system (`self`)
10. Include the `pf.conf` specific for the current system services


## Table `administrators`
```
table <administrators> persist counters file "/etc/administrators.conf"
```

These are IPs who have unrestricted access to all services running on the system, including SSH.

## Table `maintenance`
```
table <maintenance> persist counters file "/etc/maintenance.conf"
```

This table is used to activate maintenance mode on a system. This table is
usually and the most common IP to be added is `0.0.0.0/0`.

You can activate maintenance by adding the IP on the table from the command line
```sh
pfctl -t maintenance -T add 0.0.0.0/0
```

Or if you plan on rebooting the server and you'd like to ensure the system gets back online in maintenance mode
```sh
echo "0.0.0.0/0" > /etc/maintenance.conf
pfctl -t maintenance -f /etc/maintenance.conf -T load
```

## Table `moderators`
```
table <moderators> persist counters file "/etc/moderators.conf"
```
The `moderators` table holds IPs for users who will have access to egress
services provided by the applications. This means ports 80/tcp & 443/tcp for
frontend & backend and port 1194/udp for vpn servers.

## Tables `registry_clients` and `registry_servers`
```
table <registry_clients> persist counters "/etc/registry_clients.conf"
table <registry_servers> persist counters "/etc/registry_servers.conf"
```
As their names suggest, `registry_clients` lists IP's that are allowed to connect to docker `registry_servers`.


## Table `targets`
```
table <targets> persist counters file "/etc/targets.conf"
```

Table that holds the IPs of the (active) targets that users have unrestricted access to. This table is maintained by the backend console command `backend/yii cron/pf`.

## Tables `offense_network` and `offense_activated`
```
table <offense_activated> persist counters { 10.10.0.0/16 }
table <offense_network> persist counters { 10.10.0.0/16 }
```

The `offense_network` lists networks allocated to the players through VPN or DHCP installations. The `offense_activated` lists IPs or networks that are for players who have activated.

These two have tables will have the same IP's on most installations. This feature is used for installations where the users authenticate and activate their registration with ways other than the frontend website.

## Table `banned`
```
table <banned> persist counters
```
Dynamic table used to temporarily block specific IPs or network ranges.

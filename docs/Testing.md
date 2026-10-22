# Testing
The following document covers some examples on testing a variety of platform aspects, scripts and operations


## `echoctf_updown_mysql.sh` script
Needs `findings-federated.sql` functions `VPN_LOGIN()`/`VPN_LOGOUT()`/`get_player_pf_networks()`. Also modify the top values with your memcache, db and username password for the database.

```bash
common_name=1 ifconfig_pool_remote_ip=10.10.0.23 untrusted_ip=1.1.1.1 script_type="client-connect" bash contrib/echoctf_updown_mysql.sh
common_name=1 ifconfig_pool_remote_ip=10.10.0.23 untrusted_ip=1.1.1.1 script_type="client-disconnect" bash contrib/echoctf_updown_mysql.sh
```

* `common_name`: The certificate CN, usually the ID of the player.
* `ifconfig_pool_remote_ip`: Local assigned IP
* `untrusted_ip`: Remote IP
* `script_type`: Type of execution (`client-connect` or `client-disconnect`)

After run you should see output at /tmp/updown.log (on OpenBSD this will execute the `pfctl` commands for table creations).

## VPN connections per IP limit
Needs the `vpn_ip_limit` sysconfig key set to a number greater than `0` and the `notification`, `vpn_ip_whitelist` and `player_vpn_allowance` federated tables from `findings-federated.sql`. Connect two players from the same remote IP, the second one should be rejected with `IPLIMIT` and receive a notification.

```bash
common_name=1 ifconfig_pool_remote_ip=10.10.0.23 untrusted_ip=1.1.1.1 script_type="client-connect" bash contrib/echoctf_updown_mysql.sh
common_name=2 ifconfig_pool_remote_ip=10.10.0.24 untrusted_ip=1.1.1.1 script_type="client-connect" bash contrib/echoctf_updown_mysql.sh
```

Whitelisted networks (`VPN IP Whitelist` on the backend), `all_players_vip` and products with the `vpn_ip_limit` metadata key bypass or raise the limit.

## Bandwidth logging

```sql
CALL VPN_BANDWIDTH_LOG(1, INET_ATON('10.10.0.6'), 5242880, 1048576, 300);

-- Longer session, heavier usage (~150MB down, ~50MB up, 1 hour)
CALL VPN_BANDWIDTH_LOG(2, INET_ATON('10.10.0.10'), 157286400, 52428800, 3600);

-- Very short session, same player reconnecting (~100KB down, ~50KB up, 15 seconds)
CALL VPN_BANDWIDTH_LOG(1, INET_ATON('10.10.0.6'), 102400, 51200, 15);
```

## VPN Churn Check / Rate limit connect/disconnect
Test from a VPN server and ensure the rate-limit was hit!

```bash
for i in 1 2 3 4 5 6 7 8 9 10 11; do su -s /bin/ksh _openvpn -c 'script_type=client-connect common_name=1 ifconfig_pool_remote_ip=10.10.255.250 untrusted_ip=127.0.0.1 /etc/openvpn/echoctf_updown_mysql.sh'; echo "run $i rc=$?"; done; grep "hit the rate-limit" /tmp/openvpn-updown.log
```

Once the test is over dont forget to disconnect the player so that they can login again

```bash
su -s /bin/ksh _openvpn -c 'script_type=client-disconnect common_name=1 ifconfig_pool_remote_ip=10.10.255.250 untrusted_ip=127.0.0.1 /etc/openvpn/echoctf_updown_mysql.sh'
```

If you want to just confirm that the vpn_churn_check is functional you can also execute the following

```bash
for i in 1 2 3 4 5 6 7 8 9 10 11; do 
  /usr/local/bin/vpn_churn_check -s /var/run/memcached/memcached.sock 2 connect
  echo $?
done
```

## Target Instances
Create a `target_instance` record per player, per target, per server. The following query will emulate X target "spawns" as if they all came from players. The VPN server will process the spawn requests and attempt to start the machines on their corresponding docker server.

```sql
INSERT INTO target_instance (target_id, player_id, server_id, reboot,ip)
SELECT
    t.id,
    p.id,
    s.id,
    0,
    null
FROM (SELECT id, ROW_NUMBER() OVER (ORDER BY id) - 1 AS rn FROM target) t
JOIN (SELECT id, ROW_NUMBER() OVER (ORDER BY id) - 1 AS rn FROM player) p
    ON p.rn = t.rn % (SELECT COUNT(*) FROM player)
JOIN (SELECT id, ROW_NUMBER() OVER (ORDER BY id) - 1 AS rn FROM server) s
    ON s.rn = t.rn % (SELECT COUNT(*) FROM server);
```

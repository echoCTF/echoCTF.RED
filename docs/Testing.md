# Testing


## Test `echoctf_updown_mysql.sh` script
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

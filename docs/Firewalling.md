# Firewalling rules

* Allow access to port 1194 by anyone (on the host or forward the port 1194 to the echoctfred_vpn:1194)
* Allow vpn users `10.10.0.0/16` to access docker target IP's `10.0.160.0/24`
* Block vpn users `10.10.0.0/16` access to `10.0.160.254` (this is the interface ip docker assigns automatically)
* Allow targets `10.0.160.0/24` access to `10.10.0.0/16` (for reverse shells to work)

__NOTE:__ The following example is not tested you will have to adapt it according the rules mentioned above
```sh
apt-get install -y iptables-persistent
cat >/etc/iptables/rules.v4<<__EOF__
*filter

# Allows all loopback (lo0) traffic and drop all traffic to 127/8 that doesn't use lo0
-A INPUT -i lo -j ACCEPT
-A INPUT ! -i lo -d 127.0.0.0/8 -j REJECT

# Allow tarets to communicate with vpn and vpn clients
-A INPUT -s 10.0.160.0/24 -d 10.0.160.1 -j ACCEPT
-A INPUT -s 10.0.160.0/24 -d 10.10.0.0/16 -j ACCEPT

# Reject vpn users and containers access to any other host
-A INPUT -s 10.10.0.0/16  -j REJECT
-A INPUT -s 10.0.160.0/24 -j REJECT

COMMIT
__EOF__
iptables-restore < /etc/iptables/rules.v4
```

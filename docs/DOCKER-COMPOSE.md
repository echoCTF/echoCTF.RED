# docker-compose instructions
The `docker-compose.yml` builds and starts all the needed applications and targets on a single host running docker.

![echoCTF.RED docker-compose topology](/docs/docker-compose-topology.png?raw=true&1)


The docker containers use the following networks
* echoctfred_public

Build and start the containers
```sh
docker-compose up --build
```

Configure mail address for player registrations
```sh
docker exec -it echoctfred_vpn ./backend/yii sysconfig/set mail_from dontreply@example.red
```

Create backend and frontend users to test
```
# syntax: ./backend/yii user/create <username> <email> <password>
docker exec -it echoctfred_vpn ./backend/yii user/create echothrust info@echothrust.com echothrust
# syntax: ./backend/yii player/register <username> <email> <fullname> <password> <offense/defense> <0:inactive/1:active>
docker exec -it echoctfred_vpn ./backend/yii player/register echothrust info@echothrust.com echothrust echothrust offense 1
```

Set the IP or FQDN that participants will openvpn
```
docker exec -it echoctfred_vpn ./backend/yii sysconfig/set vpngw 172.22.0.4
# or
docker exec -it echoctfred_vpn ./backend/yii sysconfig/set vpngw vpn.example.red
```

Ensure that the docker containers can communicate with the participants. Once the `echoctfred_vpn` host is up run this on the host you run docker-compose at.
```sh
sudo route add -net 10.10.0.0/16 gw 10.0.160.1
```

You can also manipulate a particular container routing table by following the
example below. However keep in mind that this `route` will be deleted when the
container restarts, so the command above `route add -net`, is preferred.
```sh
pid=$(docker inspect -f '{{.State.Pid}}' echoctfred_target1)
sudo mkdir -p /var/run/netns
sudo ln -s /proc/$pid/ns/net /var/run/netns/$pid
sudo ip netns exec $pid ip route del default
sudo ip netns exec $pid ip route add default via 10.0.160.1
```

Login to the backend (http://localhost:8080/) and add a target with the following details

* Name: `echoctfred_target1`
* FQDN: `echoctfred_target1.example.com`
* Status: `online`
* Scheduled at: _empty_
* Difficulty: `0`
* Active: ✓
* Rootable: ✓
* Suggested XP: 0
* Required XP: 0
* Purpose: `this will appear when participants tweet about targets`
* Description: `this will to participants on the frontend`
* IP Address: `10.0.160.2` \
_Same as `target1` entry from `docker-compose.yml`_
* MAC Address: `02:42:0a:00:a0:02`
* Dns: `8.8.8.8`
* Net: `echoctfred_targets`
* Server: `tcp://172.24.0.254:2376`
* Image: `nginx:latest`
* Parameters: `{"hostConfig":{"Memory":"512"}}`

Once the target is created, click the Spin button on top to start it up. If
everything is correct you should be able to see the container running
```sh
docker inspect echoctfred_target1
```

Keep in mind that you will have to configure firewall rules in order to limit
or restrict who can access the target containers as well as what the target
containers will be allowed to access. Keep in mind that these targets are meant
to be hacked, so take care at limiting their network access with iptables.

RULES:
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

# docker-compose instructions
The `docker-compose.yml` builds and starts all the needed applications and targets on a single host running docker.

Keep in mind that this may require a lot of memory to run (our tests are
performed on systems with at least 8GB ram).

The docker containers use the following networks
* ___echoctfred_public___: `172.26.0.0/24`
* ___echoctfred_private___: `172.24.0.0/24`
* ___echoctfred_targets___: `10.0.160.0/24`
* ___OpenVPN___: `10.10.0.0/16`

Furthermore the following ports are maped on the host server and containers
* udp 0.0.0.0:1194 => echoctfred_vpn 172.26.0.1:1194 openvpn
* tcp 0.0.0.0:8082 => echoctfred_backend 172.26.0.2:80
* tcp 0.0.0.0:8080 => echoctfred_frontend 172.26.0.3:80

The following volumes are configured and used
* `echoctfred_data-mysql` For persistent mysql data
* `echoctfred_data-openvpn` For persistent openvpn data
* `echoctfred_data-challenges` under backend & frontned `/var/www/echoCTF.RED/*/web/uploads`
* `./themes/images` under `/var/www/echoCTF.RED/*/web/images` for logos and images

The following diagram illustrates the docker networks and containers that are configured by `docker-compose`.
![echoCTF.RED docker-compose topology](/docs/docker-compose-topology.png?raw=true&1)

Before you start building you are advised to generate a Github OAuth Token to
be used by the composer utility. This is needed in order to avoid hitting
Github rate limits on their API, which is used by `composer`. More information
about generating a token to use can be found @[Creating a personal access token for the command line](https://help.github.com/en/github/authenticating-to-github/creating-a-personal-access-token-for-the-command-line)

Once you've generated your token you can build the images by executing
```sh
GITHUB_OAUTH_TOKEN=MY_TOKEN_HERE docker-compose build
```
Build and start the containers
```sh
docker-compose up --build
```

Configure mail address for player registrations
```sh
docker exec -it echoctfred_vpn ./backend/yii sysconfig/set mail_from dontreply@example.red
```

Create backend and frontend users to test
```sh
docker exec -it echoctfred_vpn ./backend/yii user/create echothrust info@echothrust.com echothrust
docker exec -it echoctfred_vpn ./backend/yii player/register echothrust info@echothrust.com echothrust echothrust offense 1
```

The syntax for the commands can be found at [Console-Commands.md](/docs/Console-Commands.md)


Set the IP or FQDN that participants will openvpn
```sh
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

Make sure you configure the host dockerd daemon to have its API to listen tcp
to the new `private` network. However since the network becomes available only
after dockerd starts you will have to bind to _`0.0.0.0`_ (ie `-H tcp://0.0.0.0:2376`)

Make sure you restrict connections to this port to `echoctfred_vpn/172.24.0.1` and `echoctfred_backend/172.24.0.2` containers only.

More information about enabling docker API https://success.docker.com/article/how-do-i-enable-the-remote-api-for-dockerd


Your `frontend` is accessible at http://localhost:8080/

Login to the backend (http://localhost:8082/) and add a target with the following details

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

## Changing Defaults
The following section will explain what files you need to modify in order to change default values of the setup.

### How do i change the subnet for the VPN clients?
If you want to change the OpenVPN clients ranges you will have to update the following files and rebuild the images and the volume `openvpn-data`

Edit *`contrib/openvpn_tun0.conf`* and adapt Line 40 to your needs
```
server 10.10.0.0 255.255.0.0
```

### How do i change the subnet for the targets
If you want to modify the ranges of the targets edit `contrib/openvpn_tun0.conf` and modify the lines 41-43
```
push "route 10.0.100.0 255.255.255.0"
push "route 10.0.160.0 255.255.255.0"
push "route 10.0.200.0 255.255.255.0"
```

Update the docker-compose.yml with the respective ranges and rebuild

### How to move the VPN to another VM or host?
In order to move the VPN server to another host you will have to edit the
`docker-compose.yml` and comment out the VPN related entries.

Keep in mind that VPN host needs to be able to access the following ports from `echoctfred_db`
* `3306/tcp` MySQL
* `11211/tcp` Memcached

You will also need to move the targets on a new host to match the network
diagram on top.

Follow the [VPN SERVER Installation Guide](/docs/VPN-SERVER.md)

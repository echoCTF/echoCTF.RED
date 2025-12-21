# Docker Events
You can enable dynamic customizations to targets spawned on the docker servers based on events. This allows you to execute commands in the context of the target from the host system by directly accessing each target linux namespace. As such you can customize firewall rules per target when it starts.

This is achieved by using <https://github.com/echoCTF/docker-event-action>. Download the latest versin and place it under a location of your choosing (eg under `/opt`)

```bash
mv docker-event-action/ /opt/
cd /opt/docker-event-action/
npm install
npm install -g pm2
pm2 startup
pm2 start /opt/docker-event-action/docker-events.js
```

Edit the ctables file and modify as needed. The following example configures firewall rules on the container to accept connections on localhost interfaces and remote access from the networks `10.0.0.0/24` and `10.10.0.0/16` only.

If further adds a custom route for the network `10.194.0.0/16` via the `10.0.0.243` gateway.

```bash
NSPID=${1}
LINKFILE="/var/run/netns/${NSPID}"
mkdir -p /var/run/netns
/bin/rm -f "$LINKFILE"
ln -s "/proc/$NSPID/ns/net" "$LINKFILE"
# configure filtering
ip netns exec ${NSPID} iptables -I INPUT -j REJECT
ip netns exec ${NSPID} iptables -I INPUT -i lo -j ACCEPT
ip netns exec ${NSPID} iptables -I INPUT -m conntrack --ctstate ESTABLISHED,RELATED -j ACCEPT
ip netns exec ${NSPID} iptables -I INPUT -s 10.0.0.0/24 -j ACCEPT
ip netns exec ${NSPID} iptables -I INPUT -s 10.10.0.0/16 -j ACCEPT
ip netns exec ${NSPID} iptables -I OUTPUT -o lo -j ACCEPT
ip netns exec ${NSPID} iptables -A OUTPUT -m conntrack --ctstate ESTABLISHED -j ACCEPT

# add custom route
ip netns exec ${NSPID} ip route add 10.194.0.0/16 via 10.0.0.243
/bin/rm -f "$LINKFILE"
# Modify a file a in the container filesystem (without)
if [ -f /proc/$NSPID/root/usr/bin/pkexec ]; then
        chmod -s /proc/$NSPID/root/usr/bin/pkexec
fi
```

Install the `ctables` file

```bash
install -o root -m 0555 ctables /usr/local/bin/ctables
```

Note that you can run the daemon on other systems also in order to monitor for certain actions from a central system.

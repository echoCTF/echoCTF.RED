# Docker Events

You can enable dynamic customizations to targets spawned on the docker servers based on events. This allows you to execute commands in the context of the target from the host system by directly accessing each target linux namespace. As such you can customize firewall rules per target when it starts.

## Docker Event Action

One way this is achieved is by using <https://github.com/echoCTF/docker-event-action>. Download the latest versin and place it under a location of your choosing (eg under `/opt`)

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

## Docker Images

A simple way to clear images that havent been used for some time is by creating a simple `systemd` service unit.

1. Log images that are been requested for containers that are started

```sh
#!/bin/sh
docker events -f type=container -f event=start --format '{{.Actor.Attributes.image}}' | while read -r n; do id=$(docker image inspect -f '{{.Id}}' "$n" 2>/dev/null) && echo "$(date +%s) $id" >> /var/log/docker-image-usage.log; done
```

2. Create a service unit `/etc/systemd/system/docker-image-usage.service`

```sh
cat > /etc/systemd/system/docker-image-usage.service << EOF
[Unit]
Description=Log Docker image usage
After=docker.service
Requires=docker.service

[Service]
ExecStart=/usr/local/bin/docker-image-usage.sh
Restart=always

[Install]
WantedBy=multi-user.target
EOF
```

3. Reload systemctl and enable the service

```sh
systemctl daemon-reload
systemctl enable --now docker-image-usage
```

4. Occationally run the following to list images that are 30 days old and havent been used

```
c=$(( $(date +%s) - 30*86400 ))
docker images --no-trunc --format '{{.ID}} {{.Repository}}:{{.Tag}}' | while read -r id n; do l=$(awk -v i="$id" '$2==i{t=$1} END{print t+0}' /var/log/docker-image-usage.log); [ "$l" -lt "$c" ] && echo "$n $id"; done
```

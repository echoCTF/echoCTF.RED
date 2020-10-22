# Docker Servers
Configure a Debian system to be used as docker server. Docker servers are
systems that we control remotely in order to deploy targets that are assigned
to them.

This allows the distribution of multiple targets on any number of servers on
the network.

The following example assumes the server is named `dockerd.example.net` with IP
address `10.0.160.1` assigned on `eth0`, with a network subnet `10.0.160.0/24`, for
our targets.

For more information about docker networks take a look at https://docs.docker.com/network/

So lets start by preparing the host specific inventory for our docker server.
Copy the template `docker-server-template.yml` onto `inventories/dockers/host_vars`

```sh
cp templates/docker-server-template.yml \
 inventories/dockers/host_vars/dockerd.example.net.yml
```

Update the new file `inventories/dockers/host_vars/dockerd.example.net.yml` to
match your setup and make sure that at the following details are correct

```yml
---
ansible_host: 10.0.160.1
ansible_user: root
hostname: dockerd
fqdn: dockerd.example.net
mac: xx:xx:xx:xx:xx:xx
OS: debian
PACKAGES: []
network:
  # docker network name to create
  name: AAnet
  # allow to assign mac and ip details and have each target appear
  # as separate host on the network
  driver: macvlan
  driver_options:
    # Existing Network interface to attach the macvlan network
    parent: eth0
  ipam_options:
    subnet: '10.0.160.0/24'
    gateway: 10.0.160.254
    iprange: '10.0.160.0/24'

ETSCTF_TREASURES: []
ETSCTF_FINDINGS: []
ETSCTF_users: []
ETSCTF_authorized_keys: []
```

Create a hosts file under `inventories/dockers` for the new server by adding the name you picked (eg `dockerd.example.net`) to the hosts file under `inventories/dockers/hosts`.

```sh
echo -e "[dockers]\ndockerd.example.net" >> inventories/dockers/hosts
```


Push your changes to the server by running
```sh
ansible-playbook -i inventories/dockers runonce/docker-servers.yml
```

The playbook installs any missing packages and configures the system
accordingly.

Take a look at the `runonce/docker-servers.yml` for a list of tasks performed
on the server.

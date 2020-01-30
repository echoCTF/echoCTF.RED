# echoCTF infrastructure and target playbooks
The following commands assume you have Ansible installed.


## Preparation

Generate ssh keys that will be used for administering the CTF backend infrastructure
```sh
ssh-keygen -t rsa -C "keycomment" -f ssh_keys/ctf_rsa -N''
```

Prepare directory structure that will hold your Ansible managed inventories

```sh
mkdir -p inventories/targets/{host_vars,group_vars}
mkdir -p inventories/dockers/{host_vars,group_vars}
...
```

Create a debian system to be used as docker master. Docker masters are systems
that we control remotely in order to run the target systems assigned to them.
This allows the distribution of multiple targets on any number of servers on
the network. The following assumes the server is named `dockerd.echoctf.red`
with ip `1.2.3.4`. The server will be configure with a docker-network named
`AAnet` with ip network `4.3.2.0/24` (for more information run `docker network`
on your system).

```sh
cp templates/docker-master-template.yml inventories/dockers/host_vars/dockerd.echoctf.red.yml
cat inventories/dockers/host_vars/dockerd.echoctf.red.yml
---
ansible_host: 1.2.3.4
ansible_user: root
hostname: dockerd
fqdn: dockerd.echoctf.red
mac: xx:xx:xx:xx:xx:xx
OS: debian
PACKAGES: []
network:
  name: AAnet # docker network to create
  driver: macvlan # allow to assign mac and ip details and have each target appear as separate host on the network
  driver_options:
    parent: enp0s3 # Existing Network interface to attach the macvlan network
  ipam_options:
    subnet: '4.3.0.0/16'
    gateway: 4.3.0.254
    iprange: '4.3.2.0/24'

ETSCTF_TREASURES: []
ETSCTF_FINDINGS: []
ETSCTF_users: []
ETSCTF_authorized_keys: []
```

* XXXFIXMEXXX Create and configure host_vars and group_vars for docker servers and targets


* XXXFIXMEXXX Create a docker target

* Generate `inventories/targets/host_vars` based on `Dockerfiles` available
```sh
INVENTORY="targets"
echo "[$INVENTORY]">inventories/targets/hosts
for i in Dockerfiles/*;do
  fqdn=$(grep ^fqdn $i/variables.yml|awk '{print $2}')
  ln -fs ../../../$i/variables.yml inventories/$INVENTORY/host_vars/${fqdn}.yml
  echo -e "${fqdn}\t\t\t# "$(basename $i)>>inventories/$INVENTORY/hosts
done
```


* Apply configuration settings to docker servers required to host and run docker targets.
```sh
ansible-playbook playbooks/docker-masters.yml -i inventories/dockers
```

## Deploying

* Build the images
```
ansible-playbook gameplays/build-images.yml -i inventories/targets
```

* Build the images to remote builder machine
```
ansible-playbook gameplays/build-images.yml -i inventories/targets --extra-vars  "BUILDER=10.20.30.40"
```


* Update backend user inteface with target details
```
ansible-playbook gameplays/feed-mui.yml -i inventories/docker-targets
```

* Update specific backend user inteface url with target details
```
ansible-playbook gameplays/feed-mui.yml -i inventories/docker-targets  -e '{"mui":{"URL": "http://127.0.0.1:8080"}}'
```

* (optional) Deploy docker targets by ansible. __This is not needed since the backend scripts take care of it__
```sh
ansible-playbook gameplays/docker-targets.yml  -i inventories/docker-targets
```

## General purpose commands
* Generating DNS ZONE files (currently in SQL format)
```
ansible-playbook servers/generate-dnszones.yml  -i inventories/docker-targets
```

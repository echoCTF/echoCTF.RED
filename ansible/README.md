# echoCTF Ansible Playbooks

This folder holds the structure for the various ansible related tasks that can
be used to automate certain aspects of setting up a CTF infrastructure.

The directory structure is as follows
* `Dockerfiles/` Holds the targets to be build, with each target in its own directory.
 - `example/` an example target to use as a starting point for your own
* `files/` configuration template files, none of these is currently in use
* `maintenance/` Maintenance related playbooks
 - `clean-docker.yml` Removes all containers and images from a docker server
 - `count-treasures.yml` Counts treasures per target as defined in the host_vars
 - `generate-dnszones.yml` Creates an sql file with DNS details to be used for
 forward and reverse resolution.
 - `password-change.yml` Updates the default password for user `pi` on
 Raspberry Pi systems.
 - `targets_vultr_dns.yml` Feeds vultr with DNS A records for the targets
* `playbooks/` most commonly used playbooks for building, configuring and
feeding data to the platform
  - `build-images.yml` Build, tag and push to a private registry your docker images
  - `challenges.yml` and `_challenge.yml` used to feed challenges to the backend
  - `docker-masters.yml` Configures a docker server to be ready to run our containers
  - `feed-mui.yml` Feed all the target related data to the database through the
  backend web interface
  - `rpi-model.yml` Configures a Raspberry Pi to act electronics controller (eg for a smart city model)
  - `rpi-targets.yml` Configure a Raspberry Pi to act as a target without docker
* `templates/` Template configurations to use as a starting point

The following guide assumes you have Ansible installed and that you have
completed the installation for the frontend/backend systems.

The ansible playbooks are meant to help you develop and deploy your systems in a
consistent manner. You can skip any of the ansible steps if you are planning
for a small setup and you'd rather do things by hand.

The ansible folder holds ansible playbooks and inventories for your docker
servers and targets, more information about ansible inventories can be found at
https://docs.ansible.com/ansible/latest/user_guide/intro_inventory.html

## Using ansible
Before you start make sure the inventory folders for the docker servers and
targets exists by executing
```sh
# for the docker targets containers
mkdir -p inventories/targets/{host_vars,group_vars}
# for the docker server
mkdir -p inventories/dockers/{host_vars,group_vars}
```

Generate a set of ssh keys that will be used for administering the servers
```sh
mkdir -p ssh_keys/
ssh-keygen -t rsa -C "keycomment" -f ssh_keys/ctf_rsa -N''
```

### docker servers
Setup a Debian system to be used as docker master (docker server). Docker
masters are systems that we control remotely in order to run deploy targets
that are assigned to them.

This allows the distribution of multiple targets on any number of servers on
the network.

The following example assumes the server is named `dockerd.echoctf.red` with IP
address `10.0.160.1` assigned on `eth0`, with a network subnet `10.0.160.0/24`, for
our targets.

For more information about docker networks take a look at https://docs.docker.com/network/

So lets start by preparing the host specific inventory for our docker server.
Copy the template `docker-master-template.yml` onto `inventories/dockers/host_vars`
```sh
cp templates/docker-master-template.yml \
 inventories/dockers/host_vars/dockerd.echoctf.red.yml
```

Update the new file `inventories/dockers/host_vars/dockerd.echoctf.red.yml` to
match your setup and make sure that at the following details are correct

```yml
---
ansible_host: 10.0.160.1
ansible_user: root
hostname: dockerd
fqdn: dockerd.echoctf.red
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

Create a hosts file under `inventories/dockers` for the new server
```sh
echo -e "[dockers]\ndockerd.echoctf.red" >> inventories/dockers/hosts
```

Push your changes to the server by running
```sh
ansible-playbook -i inventories/dockers playbooks/docker-masters.yml
```

The playbook installs any missing packages and configures the system
accordingly.

Take a look at the `playbooks/docker-masters.yml` for a list of tasks performed
on the server.

### docker targets
Each target must reside on its own directory under `Dockerfiles/`. Each target folder has the following structure
* `autoregister.yml` An ansible playbook that is executed during the docker image build. Most of the times you dont have to modify this file.
* `Dockerfile` This includes your standard `Dockerfile` instructions in order to build your target image
* `entrypoint.sh` The script executed when the container starts
* `README.md` A readme explaining the target, solution and other details (this is empty but you are advised to keep detailed records here as it will become harder to recall details the more targets you got)
* `variables.yml` The variables used by the `autoregister.yml` playbook to do its job

You can use the existing `Dockerfiles/example` to get started or create your own.

Edit the file `Dockerfiles/example/variables.yml` and start by modifying the basic information.
```yml
ansible_host: 10.0.160.3
DOCKER: localhost
mac: "de:ad:be:ef:c0:ff:ee"
hostname: example
fqdn: example.echocity-f.com
rootable: 0 # 1 for yes
difficulty: 3
#scheduled_at: "YYYY-mm-dd HH:MM:SS"
```

The meaning and use of the variables is:
* `ansible_host` the IP address that the container will be assigned. During
development (eg while testing `docker build`) the default IP is usually one
from the `172.17.0.0/24` subnet
* `DOCKER` The docker host that will run this container (eg `dockerd.echoctf.red`)
* `mac` A valid and unique mac address. A nice trick to generate mac addresses for your hosts is to run something like the following
```sh
printf  "02:42:%.2x:%.2x:%.2x:%.2x\n" $(echo "IP_ADDRESS_OF_TARGET"|sed -e 's/\./ /g')
```
* `hostname` and `FQDN` for the target. The hostname is also used as the container name when started
* `rootable` Wether or not this target can be rooted or not `0=non-rootable` and `1=rootable`
* `difficulty` A difficulty score for the target between 0 and 5. With 0 being the easiest and 5 harder
* `scheduled_at` Uncomment to schedule this target to power up at a specific date and time


After the basic information, container specific details are included.
```yml
container:
  name: "{{hostname}}"
  hostname: "{{fqdn}}"
  build: "example" # The current folder name
  image: "example" # The current folder name
  state: "started"
  mac_address: "{{mac}}"
  purge_networks: "yes"
#  tag: "v0.1"
#  buildargs:
#   var_name: var_value
  env:
    ETSCTF_FLAG: "ETSCTF_{{ETSCTF_ENV_FLAG}}"
  dns_servers:
    - "10.0.0.254"
  networks:
    - { name: AAnet, ipv4_address: "{{ansible_host}}" }
  volumes: []
```

These details you need to modify include
* `build` and `image` The folder that we are going to build the image from and the name that the intermediate image will have. When the images are pushed to a registry the name changes to `hostname`
* `tag` A version/build tag. If no tag is defined then the default tag is `latest`
* `buildargs` Key/Value pair of variables needed to build the container. More details about this can be found at https://docs.docker.com/engine/reference/commandline/build/#set-build-time-variables---build-arg
* `env` Key/Value environment variables defined upon starting a container. More details about the docker `--env` can be found at https://docs.docker.com/engine/reference/commandline/run/#set-environment-variables--e---env---env-file
* `dns_servers` A list of dns servers that the container will use for name resolution
* `networks` The network this container will be attached. This is the same name as the one we created on our docker server (eg `AAnet`)
* `volumes` A list of volumes to be mapped when the container starts

After that we can configure the core flags for `/root`, `env`, `/etc/shadow` and `/etc/passsw`
```yml
ETSCTF_ROOT_FLAG: ""
ETSCTF_ENV_FLAG: ""
ETSCTF_SHADOW_FLAG: ""
ETSCTF_PASSWD_FLAG: ""
```
We prefer to use random md5 hashes for these flags, but you can use whatever you like. The way we generate them is by using the password generator `pwgen` and piping its output to `md5sum` like so
```sh
pwgen|md5sum
```

One you have created all your docker targets prepare their hosts file we did with the `dockers` servers.
```
echo "[targets]"> inventories/targets/hosts
echo "example.fqdn.com">>inventories/targets/hosts
```

And add links from the `variables.yml` file of each container into the
`targets/host_vars` like the following example
```sh
ln -s ../../../Dockerfiles/example/variables.yml inventories/targets/host_vars/example.fqdn.com.yml
```

Alternatively you can generate both `hosts` and their corresponding `host_vars` by running the following from the ansible folder.
```sh
INVENTORY="targets"
echo "[$INVENTORY]">inventories/$INVENTORY/hosts
for i in Dockerfiles/*;do
  fqdn=$(grep ^fqdn $i/variables.yml|awk '{print $2}')
  ln -fs ../../../$i/variables.yml inventories/$INVENTORY/host_vars/${fqdn}.yml
  echo -e "${fqdn}\t\t\t# "$(basename $i)>>inventories/$INVENTORY/hosts
done
```

In order to build the target images
```sh
ansible-playbook gameplays/build-images.yml -i inventories/targets --extra-vars "BUILDER=localhost"
```

Feed the data to the backend
```sh
ansible-playbook gameplays/feed-mui.yml -i inventories/docker-targets  -e '{"mui":{"URL": "http://127.0.0.1:8080"}}'
```

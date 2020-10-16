# Docker Servers setup guide
The following document will guide you on preparing Linux servers to act as
docker api providers for your echoCTF installation.

The Docker API providers are linux systems that we communicate with them
through docker API to start and stop containers.

___ FIXME: Add a diagram showing the server we are configuring... ___

We assume that you have configured the VPN server according to the
[VPN Server Installation](VPN-SERVER.md) as well as having a working
[Docker Registry](DOCKER-REGISTRY.md).

All the commands assume you're working from the project folder (eg. `/root/echoCTF.RED/ansible`)


The guide is based on a system base installation of `debian-10.3.0-amd64-netinst.iso` on a server with the following details

* Hostname: `dockerd160`
* interface: `enp0s3`
* IP: `10.0.160.1/24`
* GW: `10.0.160.254`
* username (during install): `sysadmin`

This is the `/etc/network/interfaces` from the system right after the installation.

```
# The primary network interface
# keep note of your own interface name
allow-hotplug enp0s3
auto enp0s3
iface enp0s3 inet static
	address 10.0.160.1
	netmask 255.255.255.0
	gateway 10.0.160.254
```


Prepare the base directory structure for ansible on the VPN server
```sh
pkg_add -vvi sshpass
mkdir -p inventories/dockers/{host_vars,group_vars}
mkdir -p ssh_keys/
ssh-keygen -t rsa -C "keycomment" -f ssh_keys/ctf_rsa -N ''
# may require: eval `ssh-agent`
ssh-add ssh_keys/ctf_rsa
```

Copy the `docker-server-advanced.yml` to prepare it for `dockerd160`
```sh
cp templates/docker-server-advanced.yml inventories/dockers/host_vars/dockerd160.yml
```

Modify the file `inventories/dockers/host_vars/dockerd160.yml` to match your settings, for our setup this is
```yml
---
ansible_host: 10.0.160.1
ansible_user: sysadmin
ansible_python_interpreter: /usr/bin/python3
hostname: dockerd160
fqdn: dockerd160.example.net
mac: xx:xx:xx:xx:xx:xx
OS: debian
DOCKER_REGISTRY: "10.0.160.254:5000"
DOCKER_REPOSITORY: "echoctfred"
PACKAGES: []
network:
  name: echoctfred_targets
  driver: macvlan
  driver_options:
    parent: enp0s3
  ipam_options:
    subnet: '10.0.160.0/24'
    gateway: 10.0.160.254
    iprange: '10.0.160.0/16'
ETSCTF_TREASURES: []
ETSCTF_FINDINGS: []
ETSCTF_users: []
ETSCTF_authorized_keys:
 - { user: "root", key: "../ssh_keys/ctf_rsa.pub" }
 - { user: "sysadmin", key: "../ssh_keys/ctf_rsa.pub" }
aptKeys:
 - { key: "https://download.docker.com/linux/debian/gpg", state: "present" }
aptRepos:
 - { repo: "deb [arch=amd64] https://download.docker.com/linux/debian buster stable", state: "present"}
apt:
 - { name: "python3-pip", state: "present", stage: 'preInst'}
 - { name: "open-vm-tools", state: "present", stage: 'preInst' }
 - { name: "python3-setuptools", state: "present", stage: 'preInst' }
 - { name: "docker-ce", state: "present" }
 - { name: "apt-transport-https", state: "present", stage: 'preInst' }
 - { name: "ca-certificates", state: "present", stage: 'preInst' }
 - { name: "curl", state: "present", stage: 'preInst' }
 - { name: "gnupg2", state: "present", stage: 'preInst' }
 - { name: "software-properties-common", state: "present", stage: 'preInst' }
pip:
 - { name: "docker", version: "*", state: "present" }
#sync:
#  - { src: "../files/docker/build", dst: "/opt" }
```

Ensure you modified the following values according to your setup

* `ansible_host: 10.0.160.1` Change the IP for the host
* `ansible_user: sysadmin` The user that is allowed to ssh into the host and use the `su` command
* `DOCKER_REGISTRY: "10.0.160.254:5000"` Change the registry IP for your setup
* `parent: enp0s3` Change the interface name to your existing one
* `subnet: '10.0.160.0/24'` Change according to your setup
* `gateway: 10.0.160.254` Change according to your setup
* `iprange: '10.0.160.0/24'` Change according to your setup
* `ETSCTF_authorized_keys:` Append any authorized_keys you would like to be added to the remote server

Create a hosts file under `inventories/dockers` for the server
```sh
echo -e "[dockers]\ndockerd160" >> inventories/dockers/hosts
```

Push your changes to the server by running ansible-playbook. Ansible will try
to connect based on the user defined on the `dockerd160.yml` file. The first
password is for the SSH to connect with the user supplied (eg `sysadmin`) and
the second is for becoming root through `su`.
```sh
ansible-playbook -K -k -i inventories/dockers runonce/docker-servers.yml
```

**NOTE:** The options `-k` asks for ssh password and `-K` asks for the
`su` password for `root`. For more information on the ansible options `-K` and
`-k`, take a look at the following page https://docs.ansible.com/ansible/2.3/become.html#command-line-options

Once this the playbook is complete without failures, you will have to restart
your docker server (`dockerd160`) for all the changes to activate.

If you're using virtual machines for the docker servers make sure you allow
promiscuous mode settings and as well as mac address changes. This is needed
for each of the targets to appear on the target network as individual hosts.

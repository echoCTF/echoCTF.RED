# Docker Servers setup guide
The following document will guide you on preparing Linux servers to act as
docker api providers for your echoCTF installation.

The Docker API providers are linux systems that we communicate with them
through docker API to start and stop containers.

___ FIXME: Add a diagram showing the server we are configuring... ___

We assume that you have configured the VPN server according to the
[VPN Server Installation](ansible/VPNGW.md) as well as having a working
[Docker Registry](ansible/DOCKER-REGISTRY.md).

All the commands assume you're working from the project folder (eg. `/root/echoCTF.RED/ansible`)


The guide is based on a system base installation of `debian-10.3.0-amd64-netinst.iso` on a server with the following details

* Hostname: `docker100`
* interface: `enp0s3`
* IP: `10.0.100.1/24`
* GW: `10.0.100.254`
* username (during install): `sysadmin`

This is the `/etc/network/interfaces` from the system right after the installation.

```
# The primary network interface
# keep note of your own interface name
allow-hotplug enp0s3
auto enp0s3
iface enp0s3 inet static
	address 10.0.100.1
	netmask 255.255.255.0
	gateway 10.0.100.254
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

Copy the `docker-server-advanced.yml` to prepare it for `docker100`
```sh
cp templates/docker-server-advanced.yml inventories/dockers/host_vars/docker100.yml
```

Modify the files under `inventories/dockers/group_vars/all.yml` and `inventories/dockers/host_vars/docker100.yml` to match your settings

Ensure you modified the following values according to your setup

* `ansible_host: 10.0.100.1` Change the IP for the host
* `ansible_user: sysadmin` The user that is allowed to ssh into the host and use the `su` command
* `DOCKER_REGISTRY:` Change the registry IP `"10.0.100.254:5000"` for your setup
* `parent: enp0s3` Change the interface name to your existing one (the one connected to the targets network)
* `subnet: '10.0.100.0/24'` Change according to your setup
* `gateway: 10.0.100.254` Change according to your setup
* `iprange: '10.0.100.0/24'` Change according to your setup
* `ETSCTF_authorized_keys:` Append any authorized_keys you would like to be added to the remote server

If you have created other docker servers make sure you include them into the inventory file under `inventories/dockers/hosts`.

Push your changes to the server by running ansible-playbook. Ansible will try
to connect based on the user defined on the `docker100.yml` file. The first
password is for the SSH to connect with the user supplied (eg `sysadmin`) and
the second is for becoming root through `su`.
```sh
ansible-playbook -K -k -i inventories/dockers runonce/docker-servers.yml
```

**NOTE:** The options `-k` asks for ssh password and `-K` asks for the
`su` password for `root`. For more information on the ansible options `-K` and
`-k`, take a look at the following page https://docs.ansible.com/ansible/2.3/become.html#command-line-options

Once this the playbook is complete without failures, you will have to restart
your docker server (`docker100`) for all the changes to activate.

If you're using virtual machines for the docker servers make sure you allow
promiscuous mode settings and as well as mac address changes. This is needed
for each of the targets to appear on the target network as individual hosts.

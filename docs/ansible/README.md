# Ansible Infrastructure

This folder holds the structure for the various ansible related tasks that can
be used to automate certain aspects of setting up and maintaining a single or
multiple CTF networks.

The directory structure consists of:

* `Dockerfiles/` Holds the targets to be build, with each target in its own directory.
* `examples/` various example files
* `files/` configuration files to be copied verbatim
* `generators/` playbooks that assist in generating files based on target, docker and challenge data
* `inventories/` our infrastructure inventories. Everything is held in these inventories.
* `maintenance/` Maintenance related playbooks
* `playbooks/` most commonly used playbooks for building, configuring and feeding data to the platform
* `runonce/` Playbooks used to setup specific operations for servers. These playbooks are usually run only once during the server setups.
* `templates/` Template configurations to use as a starting point

The following guide assumes you have Ansible installed and that you have
completed the installation for the frontend/backend systems.

The ansible playbooks are meant to help you develop and deploy your systems in a
consistent manner. You can skip any of the ansible steps if you are planning
for a small setup and you'd rather do things by hand.

The ansible folder holds ansible playbooks and inventories for your docker
servers and targets, more information about ansible inventories can be found at
[Inventory Intro](https://docs.ansible.com/ansible/latest/user_guide/intro_inventory.html)


## !!!IMPORTANT!!! Preparing SSH

Ansible is relying heavily on ssh to contact the necessary systems.

You need to make sure that you can access the systems with your prefered names or IP's before you run any playbooks.

Edit the provided `ssh_config` and update according to your needs. Make sure paths and domains are as they should.

The setup assumes that each server has at least 2 network interfaces (and some 3). Make sure that you check the documentation for each of the playbooks for details on how these interfaces are configured.

The IP's mentioned in this config file always refer to the `egress` interface (the interface that has a default gateway). Once the systems are configured you access the systems through the VPN if you like by using `ProxyCommand` (example included into the `ssh_config`).

Once everything is configured edit your `~/.ssh/config` and on top of the file add the following line

```config
Include /path/to/the/above/ssh_config
```

Generate a set of ssh keys that will be used for administering the servers and make sure you include the `IdentityFile` directive to the `ssh_config`.

```sh
mkdir -p ssh_keys/
ssh-keygen -t rsa -C "keycomment" -f ssh_keys/ctf_rsa -N ''
```

When you're done you can test that everything is working by trying to connect to the systems by their names

```sh
ssh db
ssh mui
ssh vpn
ssh pui
ssh scores
ssh registry
```

## Prepare the inventory

Before you start make sure the inventory folders for the docker servers and
targets exists.

These inventories assume that everything was installed using the playbooks from `runonce/`.

The following inventory folders exist

* `challenges/` inventory for challenges following questionn/answer format
* `dockers/` inventory for the docker machines running docker api
* `servers/` inventory for the servers of the infrastructure (other than dockers)
* `targets/` inventory of the targets


It is good to familiarize your self with ansible inventories, groups and host selection before going further.

### Challenges

Inventory for challenges following question/answer format. These are yml files that our playbooks can use to feed challenges to the online platform.

### Dockers

Inventory for the docker machines running docker api. These are Linux Debian systems that are responsible for running the targets.

### Servers

Inventory for the servers of the infrastructure (other than dockers). This inventory comes with a default inventory and sample settings and it includes the following

```text
├── group_vars
│   ├── all.yml
│   └── vpn.yml
├── hosts
└── host_vars
    ├── db.yml
    ├── mui.yml
    ├── pui.yml
    ├── registry.yml
    └── vpn.yml
```

1. Edit the `hosts` file and remove any entries that do not apply to you. In most cases you dont have to touch this files.
2. Edit the files under `host_vars` and update IP's and hostnames for the systems
3. Edit the files under `group_vars` to ensure everything matches your setup

Also confirm that ansible can communicate with the systems `ansible -i inventories/servers -m ping`

### Targets

The inventory for the docker container machines that will act as targets for the platform.

## Generators

Playbooks that assist in generating files based on target, docker and challenge data

* `password-challenge.yml` Creates a password cracking challenge
* `target-dns2sql.yml` Creates an sql file with DNS details to be used for

## Maintenance

* `clean-docker.yml` Removes all containers and images from a docker server
* `count-treasures.yml` Counts treasures per target as defined in the host_vars forward and reverse resolution.
* `password-change.yml` Updates the default password for user `pi` on Raspberry Pi systems.
* `targets_vultr_dns.yml` Feeds vultr with DNS A records for the targets

## Playbooks

Μost commonly used playbooks for building, configuring and feeding data to the platform

* `build-images.yml` Build, tag and push to a private registry your docker images
* `feed-challenges.yml` used to feed challenges to the backend
* `feed-targets.yml` Feed all the target related data to the database through the backend web interface

## runonce

Playbooks used to setup specific operations for servers. These playbooks are usually run only once during the server setups. As such they are specifically configured so that they do not require inventories in order to run. They are meant to run as standalone scripts remotely (and if you know what you're doing localy also) in order to setup and configure the software for each of the corresponding servers.

* [`db.yml`](DB.md) Standalone playbook to setup and configure an openbsd host as database server
* [`docker-registry.yml`](DOCKER-REGISTRY.md) Configures a docker registry on an OpenBSD server
* [`docker-servers.yml`](DOCKER-SERVERS.md) Configures a docker server to be ready to run our containers
* [`mui.yml`](MUI.md) Standalone playbook to setup and configure an openbsd host as echoCTF.RED/backend server
* [`pui.yml`](PUI.md) Standalone playbook to setup and configure an openbsd host as echoCTF.RED/frontend server
* [`vpngw.yml`](VPNGW.md) Configure an OpenBSD server to act as an VPN server with findings
* `rpi-model.yml` Configures a Raspberry Pi to act as electronics controller (eg for a smart city model)
* `rpi-targets.yml` Configure a Raspberry Pi to act as a target without docker

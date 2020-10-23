# Ansible folder structure

This folder holds the structure for the various ansible related tasks that can
be used to automate certain aspects of setting up and maintaining a single or
multiple CTF networks.

The directory structure consists of:

* `Dockerfiles/` Holds the targets to be build, with each target in its own directory.
 - `example/` an example target to use as a starting point for your own
* `files/` configuration template files, none of these is currently in use
* `generators/` playbooks that assist in generating files based on target, docker and challenge data
  - `target-dns2sql.yml` Creates an sql file with DNS details to be used for
* `inventories/` our infrastructure inventories. Everything is held in these inventories.
  - `challenges/` inventory for your challenges
  - `dockers/` inventory for the servers running docker api
  - `targets/` inventory of the targets
* `maintenance/` Maintenance related playbooks
 - `clean-docker.yml` Removes all containers and images from a docker server
 - `count-treasures.yml` Counts treasures per target as defined in the host_vars forward and reverse resolution.
 - `password-change.yml` Updates the default password for user `pi` on Raspberry Pi systems.
 - `targets_vultr_dns.yml` Feeds vultr with DNS A records for the targets
* `playbooks/` most commonly used playbooks for building, configuring and feeding data to the platform
  - `build-images.yml` Build, tag and push to a private registry your docker images
  - `feed-challenges.yml` used to feed challenges to the backend
  - `feed-targets.yml` Feed all the target related data to the database through the backend web interface
* `runonce/` Playbooks used to setup specific operations for servers. These playbooks are usually run only once during the server setups.
  - `db.yml` Standalone playbook to setup and configure an openbsd host as database server
  - `docker-registry.yml` Configures a docker registry on an OpenBSD server
  - [`docker-servers.yml`](DOCKER-SERVERS.md) Configures a docker server to be ready to run our containers
  - `mui.yml` Standalone playbook to setup and configure an openbsd host as echoCTF.RED/backend server
  - `pui.yml` Standalone playbook to setup and configure an openbsd host as echoCTF.RED/frontend server
  - `rpi-model.yml` Configures a Raspberry Pi to act as electronics controller (eg for a smart city model)
  - `rpi-targets.yml` Configure a Raspberry Pi to act as a target without docker
  - `vpngw.yml` Configure an OpenBSD server to act as an VPN server with findings
* `templates/` Template configurations to use as a starting point

The following guide assumes you have Ansible installed and that you have
completed the installation for the frontend/backend systems.

The ansible playbooks are meant to help you develop and deploy your systems in a
consistent manner. You can skip any of the ansible steps if you are planning
for a small setup and you'd rather do things by hand.

The ansible folder holds ansible playbooks and inventories for your docker
servers and targets, more information about ansible inventories can be found at
https://docs.ansible.com/ansible/latest/user_guide/intro_inventory.html

## Prepare the inventory
Before you start make sure the inventory folders for the docker servers and
targets exists by executing

## Generate SSH keys
Generate a set of ssh keys that will be used for administering the servers
```sh
mkdir -p ssh_keys/
ssh-keygen -t rsa -C "keycomment" -f ssh_keys/ctf_rsa -N ''
```

# echoCTF.RED Getting Started
echoCTF.RED supports a variety of installation methods and topology setups. This README serves as an index to the various methods and topics documented.


The first thing you need to do is start reading the [echoCTF.RED Architecture Details](ARCHITECTURE.md) and our [Onsite Infrastructure examples](Onsite-Infrastructure.md) to get a better understanding of the general topology and terminology used.

Once you've got a general understanding of the involved components for the
platform the next thing is to decide on what type of installation you would
like to have for your own setup.

The ideal setup is to have separate instances of each of the infrastructure
services like the topology used by our online platform echoCTF.RED, illustrated
on the diagram below.

<center><img src="https://raw.githubusercontent.com/echoCTF/echoCTF.RED/master/docs/assets/our-vultr-topology.png" alt="echoCTF.RED Vultr Infrastructure" width="400px"/></center>

How elaborate or not the topology gets depends on your needs and requirements.
We provide the following guides for some of most common installations, choose
the one best fit for your needs.

## All in one
**[docker-compose instructions](DOCKER-COMPOSE.md)**: Run all the services (including VPN) on a single linux host with docker-compose, useful for testing and customizing the applications as well as familiarize with the platform
<center><img src="https://raw.githubusercontent.com/echoCTF/echoCTF.RED/master/docs/assets/docker-compose-topology.png" alt="docker-compose-topology" width="400px"/></center>


## No VPN All in one
**[docker-compose no VPN server](DOCKER-COMPOSE-NOVPN.md)**: Run frontend, backend and db on a single __Linux Host__ using docker-compose, useful for preparing for your first CTF.

<center><img src="https://raw.githubusercontent.com/echoCTF/echoCTF.RED/master/docs/assets/docker-compose-novpn-topology.png" alt="docker-compose-novpn-topology" width="400px"/></center>

## Manual installations
* [echoCTF.RED Installation](INSTALL.md): OpenBSD General installation instructions of applications
* [echoCTF.RED Linux Installation Instructions](INSTALL-LINUX.md): Manual installation of all application on a single linux host
* [Build echoCTF.RED applications Docker images](BUILD-DOCKER.md)

## Extras
* [Install VPN Gateway on OpenBSD (VPN-SERVER.md)](VPN-SERVER.md)
* [Setup a Private Docker Registry (DOCKER-REGISTRY.md)](DOCKER-REGISTRY.md)
* [Docker Servers Setup Guide (DOCKER-SERVERS.md)](DOCKER-SERVERS.md)
* [Docker Targets Guide (DOCKER-TARGETS.md)](DOCKER-TARGETS.md)
* [Personalize your installation (PERSONALIZING.md)](PERSONALIZING.md)

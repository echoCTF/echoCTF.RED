# echoCTF.RED architecture details
The following document provides a generic overview of the various components of the platform.

![echoCTF.RED Components Architecture](/docs/architecture%20diagram.png?raw=true&1)


## dockerd
In order be able to manage, launch and restart targets from the web interfaces,
access to the docker server is required.

This is usually achieved by configuring a dockerd server to listen on a tcp
port (eg by adding `-H tcp://0.0.0.0:2735` on the dockerd start up options).

_Special care should be taken to only allow the moderator interface and vpn
server to access this service._

The docker servers are expected to have a maclan network configured for the
targets to attach to. More details about docker and macvlan networking can be
found at https://docs.docker.com/network/macvlan/

Tha docker containers acting as targets are assigned a unique ip that needs to
accessible by the players.

### Targets
The targets can be any docker image however a few rules are good to be followed

* develop target images that are self contained. That is the images are build in
a way that all their dependencies are configured during the image build process
(ie `docker build .`).

* develop target images that don't perform configuration steps at runtime.
Ensure your image can start without internet access and dont need to download
files or install databases during startup.

* develop each target with the premise that it has its own IP and FQDN. The
platform takes care at starting up the targets with the required options.


## vpn
The vpn server is responsible for providing access to the targets through
OpenVPN as well as acting as a gateway for the docker servers and targets.

It also provides tracking of findings, which is services running on a target
(either tcp, udp or icmp). When findings are activated for the platform, every
target port is monitored for connections by the gateway (`findingsd`) and
assigns points to the user who initiated the connection.

The VPN server needs to be able to access the central mysql database as well
as the memcache service.

## frontend
The frontend interface servers as the point of interaction between players and
the platform. It only needs to be able to access the database and memcache
service.

## backend
The backend interface allows administrators of the platform to manage aspects
of the infrastructure data. The backend needs to be able to communicate with
the mysql and memcache as well as the dockerd service (eg `2735/tcp`).

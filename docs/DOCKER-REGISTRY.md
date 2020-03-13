# Private Docker Registry Installation

It is advisable to have a local registry for your targets in order to simply deployment and maintenance.

There are many ways you can do that depending on your network topology and available resources.

The following guide will provide instructions on running your own registry.

## On VPN gateway
One of the proposed methods is to run the docker registry on your VPN gateway and limit access to the registry to `dockerd` servers.

We assume you followed the instructions from VPN-SERVER.md

Install the needed packages
```sh
pkg_add -vi go git
```

Create a user to run the registry (ie `registry`)
```sh
useradd -m registry
mkdir -p ~registry/storage
```

Install and configure the go docker registry
```sh
export GOPATH="/home/registry/go"
go get github.com/docker/distribution/cmd/registry
install -m 555 -o root -g wheel contrib/docker_registry.rc /etc/rc.d/docker_registry
install -m 444 -o root -g wheel contrib/docker-registry.yml /etc/docker-registry.yml
rcctl set docker_registry status on
rcctl start docker_registry
chown -R registry /home/registry/storage
```

## As Docker container
There is an official docker registry image available at https://hub.docker.com/_/registry
```sh
docker run -d -p 5000:5000 --restart always --name registry registry:2
```

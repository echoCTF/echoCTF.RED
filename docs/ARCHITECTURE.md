# echoCTF.RED Architecture Details
The following document provides a generic overview of the various components of the platform.

<img src="https://raw.githubusercontent.com/echoCTF/echoCTF.RED/master/docs/assets/architecture-diagram.png" alt="echoCTF.RED Components Architecture" width="400px"/>

## dockerd
In order be able to manage, launch and restart targets from the web interfaces,
access to the docker server is required.

This is usually achieved by configuring a dockerd server to listen on a tcp
port (eg by adding `-H tcp://0.0.0.0:2735` on the dockerd start up options).

_Special care should be taken to only allow the backend interface and vpn
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

* avoid using `VOLUME` in your containers, as these can leave traces behind,
even after restart

* prefer deleting and re-creating containers instead of stopping and starting them

## vpn
The vpn server is responsible for providing access to the targets through
OpenVPN as well as acting as a gateway for the docker servers and targets.

It also provides tracking of findings, which are services running on a target
(either tcp, udp or icmp). When findings are activated for the platform, every
target port is monitored for connections by the gateway (`findingsd`) and
assigns points to the user who initiated the connection.

The VPN server needs to be able to access the central mysql database as well
as the memcache service.

When a user connects to through OpenVPN a local script is executed that takes
care login/logout of the users and ensures that only a single session exist for
each player.

The script first connects to the central memcached service to see if the user
is currently online. After that the script connects to the local mysql database
which in turn connects to the central database server through the use of
Federated tables (https://mariadb.com/kb/en/about-federatedx/).

Furthermore, the backend needs to exist on the VPN configured to access the
main database and memcached for the backend console commands to operate.

## frontend
The frontend interface serves as the point of interaction between players and
the platform. It only needs to be able to access the database and memcache
service.

## backend
The backend interface allows administrators of the platform to manage aspects
of the infrastructure data. The backend needs to be able to communicate with
the mysql and memcache as well as the dockerd service.


## Infrastructure Sizing
Choosing the right setup can be a little tricky. The infrastructure needs,
depend heavily on your event specifications and requirements.

Some things that you need to consider before choosing the right setup for your
event are...

### Number of participants
The number of participants you expect will have a huge impact on many parts of
your platform. This includes the `vpn`, `frontend`, `dockerd`.

### Event duration
This may sound strange to you, but experience has shown that the participant
behavior will change depending on the duration of your event. This has to do
with the fact that long running events (eg 1-2 days) give participants more
time to think and plan their attacks and as such the number of concurrent
requests to the infrastructure are mostly minimal.

Short running events on the other hand (eg 8 hour event) lead to situations
where you will have the majority of your user base attacking the targets at
the same time, which in turn means that you will need more resources for your
setup.

_Rule of thumb, the longer the duration for your event, the smaller your
resource needs are going to be._

### Number of targets
It goes without saying that the number and type of targets you plan on running
bring their own set of requirements to the platform. The more targets you plan
on having the more resources you'd have to allocate for the docker servers.

The type of the applications running within a target container will also affect
your resource needs. Make sure you consult the documentation for the
applications you are planning on running as targets. As an example a container
running apache+php is generally more resource hungry than nginx+php.

### Use of findings
The use of findings can affect the resource and operating system needs of your
VPN. If you're planning on using _Findings_, then the VPN needs to run on an
OpenBSD server, otherwise the VPN can be any Linux server or container.

_Findings correspond to network ports for services running on targets
(eg port 80/tcp for a webserver). When users try to connect to these services,
a daemon (`findingsd`) is responsible for detecting the connection and
assigning points to the user ._

#### Medium/Large events
For medium/large events (between 100-1000 participants) it is suggested to have
each service on its own system.

Using the diagram above as an example, this means that `vpn`, `frontend`,
`backend`, `memcached/mysql` and `dockerd` servers run on their own systems.

#### Small events
For smaller events and lan-parties (less than 100 participants) you can use a
more conservative approach.

One option is to have

* One server for frontend, backend, database
* One server for VPN
* One or more servers for dockerd

Or alternatively if your resources are limited

* One server for frontend, backend, database AND vpn
* One or more servers for dockerd

#### echoCTF.RED
As an example to help you understand some of the relations of resources, number
of participants and number of targets we take the infrastructure needs of our
online long-running CTF platform __https://echoCTF.RED/__

Platform details, at the time of this writing (11/03/2020)

* Number of participants: 400 and keep on growing :)
* Event duration: for ever and ever...
* Number of targets: 30 with plans to reach 400-500
* Use of findings: Yes

The entire infrastructure is hosted on 7 servers at Vultr.com, our cloud
provider of choice... You can signup with Vultr and help us out at the same
time, by using our referral [Vultr Affiliate link](https://www.vultr.com/?ref=8475962-6G)

![Vultr network topology](assets/our-vultr-topology.png?1)

The infrastructure uses the Medium/Large setup scenario with the following system details.


**frontend.echoctf.red / db.echoctf.red**

* CPU: 1 vCore
* RAM: 1024 MB
* Storage: 32 GB NVMe


**backend.echoctf.red / vpn.echoctf.red**

* CPU: 1 vCore
* RAM: 1024 MB
* Storage: 25 GB SSD


**docker10 / docker16 / docker20**

* CPU: 1 vCore
* RAM: 2048 MB
* Storage: 64 GB NVMe

Each of the docker servers run around 10 containers.

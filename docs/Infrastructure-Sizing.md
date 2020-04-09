# Infrastructure Sizing
Choosing the right setup can be a little tricky. The infrastructure needs,
depend heavily on your event specifications and requirements.

## Size affecting factors
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

## Event Sizes
### Medium/Large events
For medium/large events (between 100-1000 participants) it is suggested to have
each service on its own system.

Using the diagram above as an example, this means that `vpn`, `frontend`,
`backend`, `memcached/mysql` and `dockerd` servers run on their own systems.

### Small events
For smaller events and lan-parties (less than 100 participants) you can use a
more conservative approach.

One option is to have

* One server for frontend, backend, database
* One server for VPN
* One or more servers for dockerd

Or alternatively if your resources are limited

* One server for frontend, backend, database AND vpn
* One or more servers for dockerd

## Size example of https://echoCTF.RED/
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

# Infrastructure Sizing
Selecting the right infrastructure depends on your event specifications and requirements.

## Factors Affecting Size

### Number of Participants
The expected participant count impacts several platform components, including `vpn`, `frontend`, and `dockerd`.

### Event Duration
Participant behavior varies with event length:

* **Long events (1–2 days)** – participants act more slowly, leading to fewer concurrent requests.
* **Short events (e.g., 8 hours)** – high concurrency requires more resources.

> Rule of thumb: longer events generally need fewer resources.

### Number of Targets
More targets require more resources for Docker servers. Application type also matters—for example, `apache+php` is more resource-intensive than `nginx+php`. Check application documentation before planning.

### Use of Findings
Using _Findings_ affects VPN requirements:

* **With Findings** – VPN must run on OpenBSD.
* **Without Findings** – any Linux server or container is sufficient.

> _Findings_ track service ports on targets (e.g., `80/tcp`) and assign points via `findingsd`.

## Event Sizes

### Medium/Large Events (100–1000 participants)
Each service should run on its own server:

* `vpn`
* `frontend`
* `backend`
* `memcached/mysql`
* `dockerd`

### Small Events (<100 participants)
You can consolidate services:

**Option 1:**

* One server for frontend, backend, database
* One server for VPN
* One or more servers for dockerd

**Option 2 (limited resources):**

* One server for frontend, backend, database, and VPN
* One or more servers for dockerd

## Example: <https://echoCTF.RED/>

Platform details (as of 11/03/2020):

* Participants: 10k+
* Event duration: continuous
* Targets: 300+
  * ~50 Always powered on
  * remaining as ondemand and private instances
* Findings: Yes
* Subscriptions: Yes
* Private Instances: Yes

Hosted on 8 Vultr servers using a Medium/Large setup:

### db.echoctf.red / frontend.echoctf.red

* CPU: 1 vCPU
* RAM: 2048 MB
* Storage: 64 GB NVMe

### backend.echoctf.red / vpn.echoctf.red

* CPU: 1 vCore
* RAM: 1024 MB
* Storage: 25 GB SSD

### docker06
Used for private instances

* CPU: 2 vCore
* RAM: 4096 MB
* Storage: 128 GB NVMe

### docker05
Used for private instances and ondemand targets

* CPU: 3 vCore
* RAM: 8192 MB
* Storage: 256 GB NVMe

### docker41
Always on targets

* CPU: 1 vCore
* RAM: 1024 MB
* Storage: 32 GB NVMe


![Vultr network topology](assets/our-vultr-topology.png?1)

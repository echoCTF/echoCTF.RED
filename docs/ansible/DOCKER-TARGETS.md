# Docker Targets
Each target must reside on its own directory under `Dockerfiles/`. Each target folder has the following structure

* `autoregister.yml` An ansible playbook that is executed during the docker image build. Most of the times you dont have to modify this file.
* `Dockerfile` This includes your standard `Dockerfile` instructions in order to build your target image
* `entrypoint.sh` The script executed when the container starts
* `README.md` A readme explaining the target, solution and other details (this is empty but you are advised to keep detailed records here as it will become harder to recall details the more targets you got)
* `variables.yml` The variables used by the `autoregister.yml` playbook to do its job

You can use the existing `Dockerfiles/example` to get started or create your own.

Edit the file `Dockerfiles/example/variables.yml` and start by modifying the basic information.
```yml
ansible_host: 10.0.160.3
DOCKER: localhost
mac: "de:ad:be:ef:c0:ff:ee"
hostname: example
fqdn: example.echocity-f.com
rootable: 0 # 1 for yes
difficulty: 3
#scheduled_at: "YYYY-mm-dd HH:MM:SS"
```

The meaning and use of the variables is:

* `ansible_host` the IP address that the container will be assigned. During
development (eg while testing `docker build`) the default IP is usually one
from the `172.17.0.0/24` subnet
* `DOCKER` The docker host that will run this container (eg `dockerd.example.net`)
* `mac` A valid and unique mac address. A nice trick to generate mac addresses for your hosts is to run something like the following
```sh
printf  "02:42:%.2x:%.2x:%.2x:%.2x\n" $(echo "IP_ADDRESS_OF_TARGET"|sed -e 's/\./ /g')
```
* `hostname` and `FQDN` for the target. The hostname is also used as the container name when started
* `rootable` Wether or not this target can be rooted or not `0=non-rootable` and `1=rootable`
* `difficulty` A difficulty score for the target between 0 and 5. With 0 being the easiest and 5 harder
* `scheduled_at` Uncomment to schedule this target to power up at a specific date and time


After the basic information, container specific details are included.
```yml
container:
  name: "{{hostname}}"
  hostname: "{{fqdn}}"
  build: "example" # The current folder name
  image: "example" # The current folder name
  state: "started"
  mac_address: "{{mac}}"
  purge_networks: "yes"
#  tag: "v0.1"
#  buildargs:
#   var_name: var_value
  env:
    ETSCTF_FLAG: "ETSCTF_{{ETSCTF_ENV_FLAG}}"
  dns_servers:
    - "10.0.0.254"
  networks:
    - { name: AAnet, ipv4_address: "{{ansible_host}}" }
  volumes: []
```

These details you need to modify include

* `build` and `image` The folder that we are going to build the image from and the name that the intermediate image will have. When the images are pushed to a registry the name changes to `hostname`
* `tag` A version/build tag. If no tag is defined then the default tag is `latest`
* `buildargs` Key/Value pair of variables needed to build the container. More details about this can be found at [docker --build-arg](https://docs.docker.com/engine/reference/commandline/build/#set-build-time-variables---build-arg)
* `env` Key/Value environment variables defined upon starting a container. More details about the docker `--env` can be found at [docker env variables](https://docs.docker.com/engine/reference/commandline/run/#set-environment-variables--e---env---env-file)
* `dns_servers` A list of dns servers that the container will use for name resolution
* `networks` The network this container will be attached. This is the same name as the one we created on our docker server (eg `AAnet`)
* `volumes` A list of volumes to be mapped when the container starts

After that we can configure the core flags for `/root`, `env`, `/etc/shadow` and `/etc/passsw`
```yml
ETSCTF_ROOT_FLAG: ""
ETSCTF_ENV_FLAG: ""
ETSCTF_SHADOW_FLAG: ""
ETSCTF_PASSWD_FLAG: ""
```
We prefer to use random md5 hashes for these flags, but you can use whatever you like. The way we generate them is by using the password generator `pwgen` and piping its output to `md5sum` like so
```sh
pwgen|md5sum
```

One you have created all your docker targets prepare their hosts file we did with the `dockers` servers.
```sh
echo "[targets]"> inventories/targets/hosts
echo "example.fqdn.com">>inventories/targets/hosts
```

And add links from the `variables.yml` file of each container into the
`targets/host_vars` like the following example
```sh
ln -s ../../../Dockerfiles/example/variables.yml inventories/targets/host_vars/example.fqdn.com.yml
```

**NOTE:** Ansible tries to access the `example.fqdn.com.yml` file relative to the `inventories/targets/host_vars` folder. You need to include the `../../../` part when creating the symbolic link for it to be able to find the variables.

Alternatively you can generate both `hosts` and their corresponding `host_vars` by running the following from the ansible folder.
```sh
INVENTORY="targets"
echo "[$INVENTORY]">inventories/$INVENTORY/hosts
for i in Dockerfiles/*;do
  fqdn=$(grep ^fqdn $i/variables.yml|awk '{print $2}')
  ln -fs ../../../$i/variables.yml inventories/$INVENTORY/host_vars/${fqdn}.yml
  echo -e "${fqdn}\t\t\t# "$(basename $i)>>inventories/$INVENTORY/hosts
done
```

In order to build the target images
```sh
ansible-playbook playbooks/build-images.yml -i inventories/targets --extra-vars "BUILDER=localhost" --extra-vars "DOCKER_REGISTRY=myregistry:5000" --extra-vars "DOCKER_REPOSITORY=targets"
```

Feed the data to the backend
```sh
ansible-playbook playbooks/feed-targets.yml -i inventories/targets  -e '{"mui":{"URL": "http://127.0.0.1:8080"}}'
```

You can also create a file at `inventories/targets/group_vars/all.yml` for the targets to include all those extra-vars details and not having to type them again
```yaml
DOCKER_REGISTRY: "myregistry:5000"
DOCKER_REPOSITORY: "targets"
TOKEN: MyMUIToken
mui:
  URL: "{{MUI_URL|default('http://localhost:8080')}}"
```

## Alternative build
You can also use the playbooks/build-squash.yml to build images with squashed layers. This however requires to add the option `"experimental": true,` to /etc/docker/daemon.json like so
```json
{
  "experimental": true,
  "insecure-registries":["myregistry:5555"]
}
```
Once done restart the docker daemon for the changes to take effect and try to build your image
```sh
ansible-playbook playbooks/build-squash.yml -i inventories/targets
```

This playbook provides a few tags to ease in managing single tasks, such as
* build: Build image
* push: Push image to registry
* rmi: Remove the image from local images

These tags can be used to perform a specific task or to skip some ie
```sh
ansible-playbook playbooks/build-squash.yml -i inventories/targets --skip-tags push,rmi
```

# WIP: Docker Targets guide
The following guide will help in creating, building and deploying new targets to your infrastructure.

The general outline of the steps required to build a target are
1. Prepare your environment
2. Decide on your scenario
3. Develop the target
4. Test the target
5. Deploy the target

Before we start with actual commands we need to understand a few more details.

Each target has its own folder with files needed for building the container as
well as files to assist in various tasks such as flag generation.

The targets have their own playbook (autoregister.yml) with specific tasks
needed during build and its own `variables.yml` that is used during build as
well as the main playbooks (such as build-images.yml, feed-mui.yml and others)

## Prepare your environment
Prepare the repository for targets building by creating the respective inventory
```sh
mkdir -p inventories/targets/{host_vars,group_vars}
echo "[targets]"> inventories/targets/hosts
```

On the system that will perform the actual docker build make sure you enable plaintext registry support by adding `insercure-registries` to the `/etc/docker/daemon.json` like so
```json
{
  "insecure-registries" : ["myregistryip:5000"]
}
```
More details about insecure registries can be found at https://docs.docker.com/registry/insecure/

## Decide on your scenario
This is the most important step, as it will determine how you will proceed with
any of the other steps.

For the purposes of this guide we will start with a simple target scenario. We
will implement the amazing paper [Smashing the stack for fun and profit from AlephOne](http://phrack.org/issues/49/14.html)

The target will be named `alephone` and will implement a stack overflow, based
on the paper example X. The system will have the following basic flags:
1. /root/ETSCTF: User is able to read any file in the filesystem. This usually
means the user got root access

2. env ETSCTF: User is able to read environment variables, either cause he/she
gained user access or is able to read information of running processes through
other means

3. /etc/shadow: User is able to read any file or is able to retrieve the
password hash for the `ETSCTF` user.

4. /etc/passwd: User is able to retrieve gecos information from the system

NOTE: In this case we don't really need that many flags but they are included
to better illustrate the capabilities of the provided tools.

The players will have to connect to a tcp port (eg, 666/tcp) and they will be
presented with a direct prompt to the entry vulnerable binary. Once exploited
they will be presented with a local suid binary implementing another of the
examples to gain root access.

## Developing your target
During the development stages you will need to build and re-build the target
multiple times. For this reason the development is advised to take place on a
linux workstation. You could use any of the existing systems to build your
images (eg build them from within dockerd160) but is not advisable as it may
leave traces behind that you dont want to.

The general development cycle goes something like this:
1. modify files
2. build docker (build not ok goto 1)
3. run target (run not ok goto 1)
4. push image to local registry or target
5. Profit


Start by copying the example target to a folder named as our target `alephone`
```sh
cp -r Dockerfiles/example Dockerfiles/alephone
cd Dockerfiles/alephone
```

Create the source code for the vulnerable example (lines 971-976 of Phrack49-14.txt)
```c
/* vulnerable.c */
// EXAMPLE_REPLACE_PLACEHOLDER
void main(int argc, char *argv[]) {
  char buffer[512];
  char ETSCTF_FLAG[]="EXAMPLE_FLAG_PLACEHOLDER";

  if (argc > 1)
    strcpy(buffer,argv[1]);
}
```

Edit and modify the `variables.yml`
```yml
---
ansible_host: 10.0.160.3
DOCKER: dockerd160
mac: "02:42:0a:00:a0:03"
hostname: alephone
fqdn: alephone.example.com
rootable: 0 # 1 for yes
difficulty: 0
#scheduled_at: "YYYY-mm-dd HH:MM:SS"
container:
  name: "{{hostname}}"
  hostname: "{{fqdn}}"
  build: "alephone" # The current folder name
  image: "alephone" # The current folder name
...
...
...
ETSCTF_TREASURES:
  - {
      name: "Discovered the ETSCTF username flag of {{fqdn}}/{{ansible_host}}",
      pubname: "Discovered an authentication flag on a web server",
      points: 100,
      player_type: offense,
      stock: -1,
      code: "THIS_IS_MY_EXAMPLE_ETSCTF_FLAG",
      replace: "EXAMPLE_FLAG_PLACEHOLDER",
      file: "/usr/src/vulnerable.c",
    }
...    
BUILD_COMMANDS:
  exec:
  - { cmd: "gcc -o /usr/local/bin/vulnerable /usr/src/vulnerable.c" }
  replace:
  - { #0
      pattern: "EXAMPLE_REPLACE_PLACEHOLDER",
      file: "/usr/src/vulnerable.c",
      value: "This was compiled for {{fqdn}}",
    }
  - { #1
      pattern: "ENVFLAG_HASH",
      file: "/usr/local/sbin/healthcheck.sh",
      value: "{{envhash}}",
    }
...
...
```



Update the `Dockerfile` to include the new file and compile it. We add these lines right after the directive `COPY healthcheck.sh`.
```
COPY vulnerable.c /usr/src/vulnerable.c
RUN gcc -o /usr/src/vulnerable /usr/src/vulnerable.c
```

Test build the image
```sh
docker build . -t alephone
...
... lots of output
Successfully built 02d07353faa7
Successfully tagged alephone:latest
```

Test run the image
```sh
docker run -it alephone
```

Once you're done tag appropriately and push your image to your registry
```sh
docker tag alephone 10.0.160.254:5000/mytargets/alephone:v0.1
docker tag alephone 10.0.160.254:5000/mytargets/alephone:latest
docker push 10.0.160.254:5000/mytargets/alephone:v0.1
docker push 10.0.160.254:5000/mytargets/alephone:latest
```

Now you are ready to add your target to the `backend` and have it deployed by
the system.

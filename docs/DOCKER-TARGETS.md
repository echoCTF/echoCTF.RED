# Docker Targets guide
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

## Decide on your scenario
This is the most important step, as it will determine how you will proceed with any of the other steps.

For the purposes of this guide we will start with a simple target scenario. We will implement the amazing paper [Smashing the stack for fun and profit from AlephOne](http://www-inst.eecs.berkeley.edu/~cs161/fa08/papers/stack_smashing.pdf)

The target will implement a stack overflow, based on the paper example X. The system will have the following basic flags:
1. /root/ETSCTF: User is able to read any file in the filesystem. This usually means the user got root access
2. env ETSCTF: User is able to read environment variables, either cause he/she gained user access or is able to read information of running processes through other ways
3. /etc/shadow: User is able to read any file or is able to retrieve the password hash for the `ETSCTF` user.
4. /etc/passwd: User is able to retrieve gecos information from the system

NOTE: In our case we dont really need that many flags but they are included to better illustrate the capabilities of the provided tools.

The players will have to connect to a tcp port (eg, 666/tcp) and they will be
presented with a direct prompt to the entry vulnerable binary. Once exploited
they will be presented with a local suid binary implementing another of the
examples to gain root access.

## Developing your target

## Building your target

## Deploying your target

# WIP: Docker Targets guide

**THE FOLLOWING DOCUMENT IS STILL A WORK IN PROGRESS**


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
well as the main playbooks (such as build-images.yml, feed-targets.yml and others)

## Prepare your environment
Prepare the repository for targets building by creating the respective inventory
```sh
mkdir -p inventories/targets/{host_vars,group_vars}
echo "[targets]"> inventories/targets/hosts
```

On the system that will perform the actual docker build make sure you enable non SSL registry support by adding `insercure-registries` to the `/etc/docker/daemon.json` like so
```json
{
  "insecure-registries" : ["myregistryip:5000"]
}
```

More details about insecure registries can be found at https://docs.docker.com/registry/insecure/

**NOTE:** Keep in mind that you will have to add this setting on any system you
will be attempting to `docker push` the image from.


## Decide on your scenario
This is the most important step, as it will determine how you will proceed with
any of the other steps.

For the purposes of this guide we will start with a simple target scenario. We
will implement a set of vulnerable applications based, among others, on the amazing paper [Smashing the stack for fun and profit from AlephOne](http://phrack.org/issues/49/14.html)

The target will be named `alephone` and will include programs that implement the following

1. a stack based overflow vulnerability
2. a format string vulnerability
3. a buffer overflow

The following flags will be included

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
4. push image to local registry or docker server
5. Profit


Start by copying the example target to a folder named as our target `alephone`
```sh
cp -r Dockerfiles/example Dockerfiles/alephone
cd Dockerfiles/alephone
```

Create the source code for the vulnerable examples

1. `stack_overflow.c`
```c
  /*
   * Author: http://phrack.org/issues/49/14.html
   */
  // EXAMPLE_REPLACE_PLACEHOLDER
  void main(int argc, char *argv[]) {
    char buffer[512];
    char ETSCTF_FLAG[]="EXAMPLE_FLAG_PLACEHOLDER";

    if (argc > 1)
      strcpy(buffer,argv[1]);
  }
```

2. `format_string.c`
```c
  /*
   * Author: http://www.cis.syr.edu/~wedu/Teaching/cis643/LectureNotes_New/Format_String.pdf
   */
  int main(int argc, char *argv[])
  {
    char user_input[100];
    scanf("%99s", user_input); /* getting a string from user */
    printf(user_input); /* Vulnerable place */
    return 0;
  }
```

3. `bof.c`
```c
  /*
   * Author: https://www.thegeekstuff.com/2013/06/buffer-overflow/
   */
  #include <stdio.h>
  #include <string.h>

  int main(void)
  {
    char buff[15];
    int pass = 0;

    printf("\n Enter the password : \n");
    gets(buff);

    if(strcmp(buff, "thegeekstuff"))
    {
        printf ("\n Wrong Password \n");
    }
    else
    {
        printf ("\n Correct Password \n");
        pass = 1;
    }

    if(pass)
    {
       /* Now Give root or admin rights to user*/
        printf ("\n Root privileges given to the user \n");
    }

    return 0;
  }
```

Modify `Dockerfile` to add our files. Add the following after line 28
```
COPY *.c /usr/src/
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
      name: "Discovered the ETSCTF flag from <code>stack_overflow.c</code>",
      pubname: "Discovered an ETSCTF flag on {{fqdn}}",
      points: 100,
      player_type: offense,
      stock: -1,
      code: "THIS_IS_MY_EXAMPLE_ETSCTF_FLAG",
      replace: "EXAMPLE_FLAG_PLACEHOLDER",
      file: "/usr/src/stack_overflow.c",
    }
...    
BUILD_COMMANDS:
  exec:
  - { cmd: "gcc -o /usr/local/bin/stack_overflow /usr/src/stack_overflow.c" }
  - { cmd: "gcc -o /usr/local/bin/bof /usr/src/bof.c" }
  - { cmd: "gcc -o /usr/local/bin/format_string /usr/src/format_string.c" }
  replace:
  - { #0
      pattern: "EXAMPLE_REPLACE_PLACEHOLDER",
      file: "/usr/src/stack_overflow.c",
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

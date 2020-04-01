# Build echoCTF.RED Docker images

## Single Image
You can run the fronend & backend applications through docker. The container
comes with services and applications pre-configured. You are advised to take a
look at the following configuration files:
* `contrib/apache2-red.conf` This is the apache2 configuration used for the
applications. You can modify and add extra settings as you please
(eg add ssl certifiates).


Clone the base repository and build the docker image with default settings
```sh
docker build -f contrib/Dockerfile . -t echoctf_red
```

You can modify the username and password for the vpn server user by providing
the following arguments during build.
```sh
docker build -f contrib/Dockerfile . -t echoctf_red  \
--build-arg VPNUSER=vpnuser --build-arg VPNUSERPASS=vpnuserpass
```

On systems with limited memory you may get similar errors during the build
process `Cannot allocate memory`. Increase your swap space by issuing something
like the following

```sh
/bin/dd if=/dev/zero of=/var/swap.1 bs=1M count=1024
/sbin/mkswap /var/swap.1
/sbin/swapon /var/swap.1
```

Start a container with the image
```sh
docker run -it echoctf_red bash
```

Create a user for the `backend` interface
```sh
./backend/yii user/create username email password
```

Create a player for the `frontend` interface
```sh
./backend/yii player/register username email fullname password offense 1
```

Set the mail from address for new registrations
```sh
./backend/yii sysconfig/set mail_from dontreply@example.red
```

Note that in order to allow registrations from the web interface you need to
also set the following sysconfig keys
```sh
./backend/yii sysconfig/set mail_fromName	"Mail From Name"
./backend/yii sysconfig/set mail_host smtp.host.com
./backend/yii sysconfig/set mail_port 25
```

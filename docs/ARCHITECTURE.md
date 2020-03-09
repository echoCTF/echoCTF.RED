# echoCTF.RED architecture details
The following document provides a generic overview of the various components of the platform.

![echoCTF.RED Components Architecture](/docs/architecture%20diagram.png?raw=true)

## dockerd
In order to manage, launch and restart targets from the web interfaces we require access to the a docker server.

This is usually achieved by configuring a dockerd server to listen on a tcp port (eg by adding `-H tcp://0.0.0.0:2376` on the dockerd start up options).

_Special care should be taken to only allow the moderator interface and vpn server to access this service._

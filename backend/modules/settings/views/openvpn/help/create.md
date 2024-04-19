Manage OpenVPN server instance configurations.
These pages allows to manage your OpenVPN instance configurations from a central location.

The fields include:
* `Provider ID`: A string that identifies the server or cloud instance this service is run on
* `Server`: Name of server this config corresponds (used for multiple vpn servers)
* `Name`: A short name for the configuration (eg the configuration filename)
* `Net`: The network this configuration will serve. (this refers to the network range that is assigned to the clients)
* `Mask`: The netmask to be applied to this network (this also used to mask the current IP of a player in order to determine what openvpn server is connected to)
* `Mgmt IP`: The IP that this openvpn will listen for administrative connections
* `Mgmt Port`: The port for the openvpn administrative service
* `Mgmt Passwd`: The password to authenticate to this service
* `Status log`: The full path to the OpenVPN status log file (make sure this matches the the path from the `status` file of openvpn conf)
* `Conf`: The configuration file contents

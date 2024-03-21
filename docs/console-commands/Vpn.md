# VPN related commands
General purpose VPN related commands.

## is-online
Check if a given player `username` or `id` is currently online

Usage: `./backend/yii vpn/is-online <player>`

## kill <player>
Kill the OpenVPN connection of a given player `username` or `id`.

Usage: `./backend/yii vpn/kill <player>`

## killall
Kill all (not just online ones) player connections from OpenVPN and set players offline.

Usage: `./backend/yii vpn/killall`


This command connects to the OpenVPN management port and issues a `kill` command for each active player which currently has an IP assigned to them. Once the OpenVPN operations are complete a cleanup is performed to reset the online status of all players.

## load
Load an OpenVPN instance configuration file into the database

Usage: `./backend/yii vpn/load <openvpn_conf>`

The command extracts the following details from the provided configuration file:
* Gets the hostname from the running host
* Player allocated Network and Netmask
* OpenVPN Management IP, port and password to authenticate
* OpenVPN status file location

## logout
Logout a given player `username` or `id`, from the database, ignoring OpenVPN sessions.

Usage: `./backend/yii vpn/load <player>`


## logoutall
Logout all players from the database. It **does NOT** issue an OpenVPN kill command for connected players.

**NOTE**: _Only issue this command when you are certain there are no users connected to OpenVPN._

## save
Save an OpenVPN instance configuration file with data from the database

Usage: `./backend/yii vpn/save <openvpn_conf>`

The command looks for a record with the following criteria:
* uses the current system `hostname` as a `server`
* uses the filename (basename) from the openvpn configuration file provided as cli argument

## status
A small wrapper for OpenVPN status files. The details are merged with data from the database.

Usage: `./backend/yii vpn/status`

The command uses the status file defined in the Backend Settings=>OpenVPN entries matching the current server.

Networks and IP addresses that are exempt from the `vpn_ip_limit` (maximum number of players connected to the VPN from the same remote IP address) sysconfig key.

If the remote (public) IP address of a connecting player matches any of the entries below the limit is not enforced for that connection.

The fields are:
* `Network`: A single IP address (eg `203.0.113.10`), an IP with prefix length (eg `203.0.113.0/24`) or an IP with netmask (eg `203.0.113.0/255.255.255.0`)
* `Description`: Notes on why this network is exempt (eg `Campus NAT`)

**NOTE**: Entries are always displayed in dotted notation.

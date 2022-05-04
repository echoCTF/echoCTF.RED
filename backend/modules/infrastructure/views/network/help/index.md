Create networks of grouped targets. These network corespond to frontend and VPN
firewall entries (depending on their defined parameters).

The fields include:
* `Codename`: The internal name used to group the firewall rules
* `Name`: A name to be displayed for the network
* `Description`: A description to display for the network
* `Public`: A flag of wether or not this network will be public (all can access) or private (restricted access)
* `Active`: A flag of wether this network is active or not
* `Icon`: An icon to be displayed on frontend
* `Weight`: A number value to determine ordering
* `Ts`: A timestamp of last modification of this record
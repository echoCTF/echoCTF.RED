The platform management of targets that participants interact.

Fields:
* `Name`: A name for the target (this is also the target hostname)
* `Fqdn`: Fully qualified name for the target (hostname + domain)
* `Difficulty`: The difficulty rating for the target
* `Category`: Free-form category classification for the target
* `Status`: The current status of the target
* `Scheduled at`: If the target has a scheduled action
* `Active`: Wether or not the target is active
* `Healthcheck`: Perform healthchecks on the target
* `Rootable`: Wether or not the target is rootable
* `Timer`: Wether or not the target completions are timed
* `Writeups Allowed`: Wether or not writeups are allowed for this target
* `Instance Allowed`: Wether or not private instances are allowed for this target
* `Require Findings`: Wether or not findings are required before we allow flags to be claimed
* `Player Spin`: Wether or not player spins are allowed
* `Headshot Spin`: Wether or not to restart the target after each headshot
* `Suggested XP`: Minimum suggested XP for the target
* `Required XP`: Minimum required XP for the target
* `Purpose`: A small tagline describing the purpose of the target
* `Description`: A full html text with the target description
* `IP Address`: A unique IP for the target
* `Mac`: A unique MAC address for the target
* `Dns`: The DNS server that will be defined on the target
* `Net`: The network name that this target will be attached
* `Server`: Server connection string for this target
* `Image`: The full image url for this target
* `Imageparams`: Image pull parameters (eg for registries that require authentication)
* `Weight`: An ordering weight
* `Parameters`: Extra parameters for docker containers (eg `{"hostConfig":{"Memory":"512"}}`)

Actions:
* `Save` the record details
* `Save and Destroy` any existing containers running with these details
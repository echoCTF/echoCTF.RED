The platform management of targets that participants interact.

Fields:
* `Name`: A short name for the target (this is also the machine name used when created)
* `Ipoctet`: IP for the target
* `Server`: Server connection string for this target
* `Network`: The network this target belongs to
* `Status`: The current status of the target
* `Scheduled at`: If the target has a scheduled action
* `Rootable`: Wether or not the target is rootable
* `Active`: Wether or not the target is active
* `Timer`: Wether or not the target completions are timed
* `Difficulty`: The difficulty rating for the target
* `Headshots`: How many headshots the target has
* `Points`: How many points total this target awards
* `Weight`: An ordering weight

The page provides the following actions:
* `Create` a new target
* `Spin All` target if they are running destroy and recreate, if they are not start them
* `Pull All` the target images on their respective servers
* `Statistics` for the targets related to their overal solves and attempted solves
* `Container Status` from all defined target server strings
* `Docker compose` file generation for the available targets
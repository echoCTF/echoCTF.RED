Link treasures/flags with actions towards external systems.
This feature allows you to define what commands need to be issued to external API's or services when a flag is claimed.

The fields are as following:
* `Treasure`: The treasure that will trigger this action when claimed
* `Ipoctet`: The IP address of the device that we will connect to
* `Port`: The port that we need to connect to
* `Command`: The command that we will send to this service
* `Weight`: Wen multiple actions on the same treasure are defined, this allows for ordering their actions
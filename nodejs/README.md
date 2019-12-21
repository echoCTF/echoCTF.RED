# node.js applications
Various node.js applications we use on our platform.


## discord-bot
A bot to provide assistance to our discord users at echoCTF Discord server.

The configuration file (`config.json`)should look something like
```json
{
  "prefix": "~",
  "allowedRole": "BUGHUNTER",
  "autoRole": "offense",
  "token": "<BOT TOKEN>",
  "dbhost": "localhost",
  "dbuser": "root",
  "dbpass": "",
  "dbname": "echoCTF"
}
```

The keys are self explanatory but in case it is not clear
* `prefix`: The prefix for the command eg `~` or `!`
* `allowedRole`: Roles that are allowed to send commands to the bot
* `autoRole`: Role to assign to new members of the guild
* `token`: Your token from discord developer portal
* `dbhost`: database host
* `dbuser`: database user
* `dbpass`: database password
* `dbname`: database name to use

#### COMMANDS

* `help`: provides help
* `myid`: returns the discord id for the user to be used on the profile settings
* `target [name]`: target short name to lookup (eg `~target bart`)
* `say`: Make the bot say something
* `purge [2-100]`: Purge messages from the channel (eg `~purge 100`)
* `leave`: Leave the guild this command was received on

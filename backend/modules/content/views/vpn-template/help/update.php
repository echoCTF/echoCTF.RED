Create OpenVPN client configuration template. These appear
under the player's profile page to be downloaded and used
with OpenVPN to connect to the VPN service and access the targets.

The form fields include:
* `ID`: A unique record ID
* `Name`: The name of the entry. This is what is being displayed to the players profile.
* `Filename`: The filename that will be used when the player tries to download this configuration file.
* `Description`: A small description for the configuration file
* `Active`: Whether the configuration is active or not. Inactive configurations do not appear to the players profile nor can they be downloaded.
* `Visible`: Whether or not the configuration file will be made visible to the players. Non visible configurations can still be accessed by going directly to the download link.
* `Client`: Whether or not this is an OpenVPN client configuration.
* `Server`: Whether or not this is an OpenVPN server configuration.
* `Content`: The actual configuration file contents. This field supports PHP code to be embedded into the configuration.
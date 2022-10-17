# SSL commands
SSL related operations. Generate and manipulate keys for OpenVPN authentication.

* Optional command line arguments are enclosed in `[]`
* Required command line arguments are enclosed in `<>`


## Create Certification Authority
Create and store CA keys in the database

Usage: `./backend/yii ssl/create-ca [fileout]`

If the optional `fileout` is set to _`1`_ the keys and certificates will also be
stored on the current directory.


## Get Certification Authority files
Get the Certificate Authority related keys and certificates from the database

Usage: `./backend/yii ssl/get-ca [fileout]`

If the optional `fileout` is set to _`1`_ the keys and certificates will be
stored on the current directory instead of stdout.


## Load existing VPN TLS Auth key into the database
Load an existing TLS Auth key file into the database

Usage: `./backend/yii ssl/load-vpn-ta [file]`


## Create Certificate Revocation List
Usage: `./backend/yii ssl/create-crl`



## Generate Certificate Revocation List
Usage: `./backend/yii ssl/generate-crl`



## Revoke player VPN keys
Revoke a given players certificates

Usage: `./backend/yii ssl/revoke <player_id>`


## Create server certificate and sign by default CA
Create and Sign certificate for Servers (openvpn, web servers etc)

Usage: `./backend/yii ssl/create-cert [commonName] [emailAddress]`


* `commonName`: Certificate common name (default: "VPN Server")
* `emailAddress`: Email address for the certificate (default: empty)

## Generate and sign Player Certificates
Generate and sign player certificates

Usage: `./backend/yii ssl/gen-player-certs <email> [fileout]`

## Generate all players certificates
Generate certificates for all players

Usage: `./backend/yii ssl/gen-all-player-certs [fileout]`

## Load Certification Authority
Load the CA required files from the local filesystem.

Usage: `./backend/yii ssl/load-ca`

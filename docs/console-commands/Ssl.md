# SSL commands
SSL related operations. Generate and manipulate keys for OpenVPN authentication.

* Optional command line arguments are enclosed in `[]`
* Required command line arguments are enclosed in `<>`


## Create Certification Authority (`./backend/yii ssl/create-ca`)
Create and store CA keys in the database.

Usage: `./backend/yii ssl/create-ca [fileout]`

## Get Certification Authority files (`./backend/yii ssl/get-ca`)
Create and store CA keys in the database.

Usage: `./backend/yii ssl/get-ca [fileout]`

## Load existing VPN TLS Auth key into the database (`./backend/yii ssl/load-vpn-ta`)
Load an existing TLS Auth key file into the database

Usage: `./backend/yii ssl/load-vpn-ta [file]`

## Create Certificate Revocation List (`./backend/yii ssl/create-crl`)


## Generate Certificate Revocation List (`./backend/yii ssl/generate-crl`)

## Revoke player (`./backend/yii ssl/revoke`)


## Create server certificate and sign by default CA (`./backend/yii ssl/create-cert`)
Create and Sign certificate for Servers (openvpn, web servers etc)

Usage: `./backend/yii ssl/create-cert [commonName] [emailAddress] [subjectAltName] [CAcert] [CAkey]`


## Generate and sign Player Certificates (`./backend/yii ssl/gen-player-certs`)
Generate and sign player certificates

Usage: `./backend/yii ssl/gen-player-certs [email] [fileout]`

## Generate all players certificates (`./backend/yii ssl/gen-all-player-certs`)
Generate certificates for all players

Usage: `./backend/yii ssl/gen-all-player-certs [fileout]`

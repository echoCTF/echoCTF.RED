# echoCTF Backend Management Interface

Configure `mui/config/params.php` and `mui/config/CA.cnf` according to you liking this is required for SSL related operations (needed by OpenVPN) to work properly.

## Import database
Import schema from `../schemas` and perform any migrations
```sh
mysqladmin create echoCTF
mysql echoCTF < ../schemas/echoCTF.sql
mysql echoCTF < ../schemas/echoCTF-routines.sql
mysql echoCTF < ../schemas/echoCTF-triggers.sql
mysql echoCTF < ../schemas/echoCTF-event.sql
./yii migrate --interactive=0
```

## VPN
You need to perform the following commands from the current path
```sh
./yii ssl/create-ca 1  # create CA certificate and store it on the current folder
./yii ssl/get-ca 1     # Get the CA certificate from the database and store it on the current folder
./yii ssl/create-cert  # Create OpenVPN server keys and certificates and store them on the current folder
```

Generate player certificates

```sh
./yii ssl/gen-all-player-certs # Generate and update database
./yii ssl/gen-player-certs email@example.com
./yii ssl/get-player-certs email@example.com 0 1 # for ccd generation
```

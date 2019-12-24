# echoCTF-mUI2
echoCTF moderators UI (Yii2 version)

Configure `mui/config/params.php` and `mui/config/CA.cnf`

## Import database
Import schema from `echoCTF-UI/schema` and execute
```
./yii migrate --interactive=0
```
## VPN
```
cd /home/moderatorUI/mui
./yii ssl/create-ca 1  # create CA certificate and store it on the current folder
./yii ssl/get-ca 1     # Get the CA certificate from the database and store it on the current folder
./yii ssl/create-cert  # Create OpenVPN server keys and certificates and store them on the current folder
```
Generate player certificates
```
./yii ssl/gen-all-player-certs # Generate and update database
./yii ssl/gen-player-certs email@example.com
./yii ssl/get-player-certs email@example.com 0 1 # for ccd generation
```


# Usefull migration commands
```
$this->executeResetSequence('tbl_name', '1'); -- RESET table states (auto_increment etc)
```

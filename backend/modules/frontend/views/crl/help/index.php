Manage the revoked player VPN certificates. Each entry is a certificate queued for revocation, the ssl/generate-crl command revokes them and rebuilds crl.pem.

The displayed fields include:
* `ID`: The record id
* `Player ID`: The player the revoked certificate belongs to
* `Subject`: The subject of the revoked certificate
* `Ts`: Timestamp of the entry

The page provides the following actions:
* `Create Crl`: Add a new revoked certificate entry
* `Delete All`: Remove every entry from the CRL

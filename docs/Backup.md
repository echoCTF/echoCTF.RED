# Backing up your echoCTF


## Backing up an echoCTF OpenBSD GW server
Many times we run events on venues that cannot support the bandwidth requirements for hosting a CTF competition, in these cases we opt to ship our own equipment to the venue before the event so that we can ensure hassle free participation.

These steps are only for gateways that run on such events with on-site gateways.
A simple way to backup everything you might need from your echoCTF GW server.

Change the commands below to reflect your installation paths and credentials.

The following commands assume that your database is named `ets_ctf`. These are
run from the OpenBSD echoCTF Gateway mostly for post event analysis on the
performance of the platform during the event.

```
mysqldump -uroot -AYKER --add-drop-database --add-drop-table --hex-blob --triggers --tz-utc > echoctfgw_ayker.sql
mysqldump -uroot -KER --add-drop-table --hex-blob --triggers --tz-utc ets_ctf > echoctfgw_ker_ets_ctf.sql
mysqldump --complete-insert --extended-insert --hex-blob --tz-utc --no-create-db --no-create-info ets_ctf > echoctfgw_data_ets_ctf.sql
tar -czf echoctfgw_etc.tgz /etc
tar -czf echoctfgw_home.tgz /home
tar -czf echoctfgw_root.tgz /root
tar -czf echoctfgw_usr_local_sbin.tgz /usr/local/sbin
tar -czf echoctfgw_var.tgz /var/!(log)
```

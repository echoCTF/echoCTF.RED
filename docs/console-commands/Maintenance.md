# Backend Maintenance Operations

* `maintenance/start` start/enter maintenance mode
* `maintenance/stop`: end/exit maintenance mode
* `maintenance/truncate-all`: truncate all
* `maintenance/truncate-instance-audit`: Truncate instance audit
* `maintenance/truncate-vpn-history`: Truncate vpn history
* `maintenance/truncate-spin-history`: Truncate spin history
* `maintenance/purge-old-notifications`: Delete old notification no matter if they have been read or not. Takes number of days back to delete (default: `40`)
* `maintenance/count-tables`: Count per table records
* `maintenance/sync-collations`: Sync tables collation with the one defined in db config

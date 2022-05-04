Target instances audit trail of all changes taking place on them.

The fields include:
* **Op**: Operation type **`i`NSERT**, **`u`PDATE**, **`d`ELETE**
* **Player**: The player that owns this instance
* **Target**: The target this instance is based on
* **Server**: The server this instance is running on (it can also be `NULL`)
* **IP**: The IP address of this instance (it can also be `NULL)`
* **Reboot**: Reboot state of the target
* **Ts**: Timestamp of the record creation

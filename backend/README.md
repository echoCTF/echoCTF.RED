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

## Custom migration templates

`migrate/create` recognizes these name patterns and pre-fills the migration body. Anything not matching falls back to the stock Yii2 template.

**Menu item**
```sh
./yii migrate/create add_menu_item_ITEM_to_PARENT_parent
# e.g.
./yii migrate/create add_menu_item_ws_token_history_to_activity_parent
```

**Trigger**
```sh
./yii migrate/create create_trigger_after_insert_on_TABLE
./yii migrate/create drop_trigger_before_update_on_TABLE
```

**Stored procedure**
```sh
./yii migrate/create create_procedure_NAME
./yii migrate/create drop_procedure_NAME
```

**Routine (function)**
```sh
./yii migrate/create create_routine_NAME_returns_TYPE
./yii migrate/create create_routine_NAME_returns_TYPE_deterministic
```

**Scheduled event**
```sh
./yii migrate/create create_event_NAME_every_N_UNIT_starts_now
# UNIT: minute|hour|day|week|month, plural optional
./yii migrate/create drop_event_NAME
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
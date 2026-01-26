# Websockets service

echoCTF.RED provides player updates to the live players through the use of [ws-server](https://github.com/echoCTF/ws-server).

The services that want to communicate an update to the current live players submit their events through the HTTP service of ws-server.

The system can send messages to a specific player or all connected players through the `/publish` and `/broadcast` endpoints respectively.

Currently the following events are implemented:

* `notification`: Sends a direct notification, Alert or Sweetalerts.
* `apiNotifications`: Tell the clients to perform an update of their in-page notifications.
* `target`: Update the target card if currently visible

## Examples

* Notify all users to perform an `apiNotifications()` js call. Effectively fetch the latest notifications through ajax.

```shell
curl -X POST "http://localhost:8888/broadcast" \
  -H "Authorization: Bearer YOURTOKEN" \
  -H "Content-Type: application/json" \
  -d '{ "event": "apiNotifications" }'
```

* Send a SweetAlert (`"type": "swap:info"`) notification to player with id `1`

```shell
curl -X POST "http://localhost:8888/publish" \
  -H "Authorization: Bearer server123token" \
  -H "Content-Type: application/json" \
  -d '{
        "player_id": "1",
        "event": "notification",
        "payload":
        {
          "title": "This is a notification",
          "body": "This is the notification body",
          "type": "swal:info"
        }
      }'
```

Note: Removing the `swal:` prefix from `type` sends a normal bootstrap alert notification.

* Send an update for to player id `1` for updates on target id `2`

```shell
curl -X POST "http://localhost:8888/publish" \
  -H "Authorization: Bearer server123token" \
  -H "Content-Type: application/json" \
  -d '{
        "player_id": "1",
        "event": "target",
        "payload": { "id": "2" }
      }'
```

This will execute the js code `targetUpdates(2)`.

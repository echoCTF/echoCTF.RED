# API Documentation
The platform exposes a REST API for public consumption.

NOTE: __This documentation is only temporary and will be replaced by a Swagger.io/Postman documentation.__

## Get headshots
 URL: `GET /api/headshots` \
 Public: Yes

* The collection envelope is called items
* Every item has the following fields
    - `profile_id`: integer The player profile id
    - `target_id`: integer The target id
    - `target_name`: string The target name
    - `timer`: integer The time in seconds for completion
    - `first`: boolean If the headshot was first
    - `rating`: integer The user provided difficulty rating for the target
    - `created_at`: datatime The headshot was achieved

**Sample item**
```json
{
  "profile_id":"177952",
  "target_id":24,
  "target_name":"tweek",
  "timer":26740,
  "first":false,
  "rating":-1,
  "created_at":"2020-09-11 04:15:47"
}
```

**Parameters:**

* `filter`: filter through `filter[field_name]=field_value` example: `filter[profile_id]=1337`
* `fields`: selecting fields through `fields=field_name,field_name...` syntax eg `fields=target_name,profile_id` to select only the target name and profile_id
* `sort`: sorting through sort eg sort=-created_at,profile_id to sort created_at descending and profile_id ascending
* `per-page`: limiting results per page through per-page eg per-page=100, accepted values in the range of [1...100]


**Examples:**

* Simple request
```sh
curl -i -H "Accept:application/json" "https://echoctf.red/api/headshots"
```

* filter only headshots for player with profile_id=31337
```sh
curl -i -H "Accept:application/json" "https://echoctf.red/api/headshots?filter[profile_id]=31337"
```

* filter only headshots for player with profile_id=31337 and get only the target names
```sh
curl -i -H "Accept:application/json" "https://echoctf.red/api/headshots?filter[profile_id]=31337&fields=target_name"
```

## Bearer Operations
For the following endpoints you will need to have a bearer token to be able to access them

* `api/target/claim`: Submit a flag for validation
* `api/target/instances`: List of instances (if any)
* `api/target/<id:\d+>`: Get details for a given target
* `api/target/<id:\d+>/spin`: spin a machine
* `api/target/<id:\d+>/spawn`: Spawn a private instance (if allowed)
* `api/target/<id:\d+>/shut`: Shutdown a private instance

### Claim Flag
URL: `POST /api/target/claim` \
POST: `{ "hash":"flag" }`

```sh
curl "https://echoctf.red/api/target/claim" \
 -H "Authorization: Bearer YOURTOKEN" \
 -H "Accept:application/json" \
 -d '{"hash":"MyFlagHere"}'
```

### Get instances
URL: `GET /api/target/instances`

Get a list of instances and depending on the platform setup may include team instances as well.
```sh
curl "https://echoctf.red/api/target/instances" \
 -H "Authorization: Bearer YOURTOKEN" \
 -H "Accept:application/json"
```
### Get target details
URL: `GET /api/target/<id:\d+>`
```sh
curl "https://echoctf.red/api/target/11" \
 -H "Authorization: Bearer YOURTOKEN" \
 -H "Accept:application/json"
```

### Spin a target
URL: `GET /api/target/<id:\d+>/spin`

Perform a spin operation depending on the type and state of the machine.
  * If machine is powered off then power up
  * If machine is powered up then schedule a reset
```sh
curl "https://echoctf.red/api/target/11/spin" \
 -H "Authorization: Bearer YOURTOKEN" \
 -H "Accept:application/json"
```

### Spawn a private instance
URL: `GET /api/target/<id:\d+>/spawn`

Spawn a private instance of a given machine (if player is allowed).
```sh
curl "https://echoctf.red/api/target/11/span" \
 -H "Authorization: Bearer YOURTOKEN" \
 -H "Accept:application/json"
```

### Shut a private instance
URL: `GET /api/target/<id:\d+>/shut`

Shut a private instance of a given machine (if exists for the given player).
```sh
curl "https://echoctf.red/api/target/11/shut" \
 -H "Authorization: Bearer YOURTOKEN" \
 -H "Accept:application/json"
```

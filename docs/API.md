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

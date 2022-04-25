Disable a specific URL route endpoints. The table has a single field:
  * **`route`**: The route to disable, supports wildcard `%` at beginning and end of a route

**NOTE**: That for effective filtering this needs to match the internal component that will handle the request and not the alias defined by `source` field of **Url Routes**.
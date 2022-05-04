Create dynamic URL routes to be used by **[yii\web\UrlManager🔗](https://www.yiiframework.com/doc/api/2.0/yii-web-urlmanager)**

The fields included are:
* **ID**: The unique id of the record (useful when debugging)
* **Source**: The source URL that the users will access (eg a source `/targets` corresponds to `example.com/targets`). This also makes sure that links produced by the platform reference this name instead of the actual destination.
* **Destination**: The internal application action that will handle this request (eg `/target/default/index`)
* **Weight**: A numeric value to determine the order which URL will be resolved. Most used URLs are on top to save time.

**NOTE**: Multiple entries of the same `source` value are also supported, however, the actual links that the platform will produce, will reference the one with the lowest `weight` value.
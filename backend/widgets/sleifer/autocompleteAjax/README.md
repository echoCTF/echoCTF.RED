yii2-autocomplete-ajax
===================
Original: https://github.com/ismaelsleifer/yii2-autocomplete-ajax

This is the AutocompleteAjax widget and a Yii 2 enhanced wrapper for the [Autocomplete | jQuery UI](https://jqueryui.com/autocomplete/). A simple way to search model id of the attributes model.

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either add

```
"require": {
    "sleifer/yii2-autocomplete-ajax": "*"
}
```

of your `composer.json` file.

## Latest Release

The latest version of the module is v0.5.0 `BETA`.

## Usage

### Find model

View:

```php
use sleifer\autocompleteAjax\AutocompleteAjax;

// Normal select with ActiveForm & model
<?= $form->field($model, 'user_id')->widget(AutocompleteAjax::classname(), [
    'multiple' => false,
    'url' => ['ajax/search-user'],
    'options' => ['placeholder' => 'Find by user email or user id.']
]) ?>
```

Controller:

```php
class AjaxController extends Controller
{
    public function actionSearchUser($term)
    {
        if (Yii::$app->request->isAjax) {

            $results = [];

            if (is_numeric($term)) {
                /** @var Tag $model */
                $model = Tag::findOne(['id' => $term]);
                
                if ($model) {
                    $results[] = [
                        'id' => $model['id'],
                        'label' => $model['email'] . ' (model id: ' . $model['id'] . ')',
                    ];
                }
            } else {

                $q = addslashes($term);

                foreach(Tag::find()->where("(`email` like '%{$q}%')")->all() as $model) {
                    $results[] = [
                        'id' => $model['id'],
                        'label' => $model['email'] . ' (model id: ' . $model['id'] . ')',
                    ];
                }
            }

            echo Json::encode($results);
        }
    }
}
```

### Google Places API Web Service


```php
<?= $form->field($model, 'address')->widget(\sleifer\autocompleteAjax\AutocompleteAjax::classname(), [
    'startQuery' => false,
    'url' => ['ajax/search-place'],
    'options' => ['placeholder' => 'Find place.'],
    'afterSelect' => 'function(event, ui) { var value = JSON.parse(ui.item.data); updateMarker(value.lat, value.lng); }'
]) ?>
```

```php
/**
 * enable "Google Places API Web Service" in https://console.developers.google.com
 **/
public function actionSearchPlace($term, $apiKey = '')
{
    $result = [];
    $results = [];

    if (Yii::$app->request->isAjax) {

        /** CURL QUERY **/

        $curl = curl_init('https://maps.googleapis.com/maps/api/place/textsearch/json?key=' . urlencode($apiKey) . '&language=en&query=' . urlencode($term));
        curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);

        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($status !== 200) {
            throw new NotFoundHttpException('Error: ' . curl_error($curl) . ' Code: ' . $status);
        } else {
            if (json_decode($response, true) && json_last_error() == JSON_ERROR_NONE) {
                $result = json_decode($response, true);
            }
        }
        curl_close($curl);

        /** CURL QUERY **/

        if (!empty($result['results'])) {
            foreach($result['results'] as $model) {
                $results[] = [
                    'id' => $model['formatted_address'],
                    'label' => $model['formatted_address'] . ' (model location: ' . json_encode($model['geometry']['location']) . ')',
                    'data' => json_encode($model['geometry']['location']),
                ];
            }
        }

        echo Json::encode($results);
    }
}
```

## License

**yii2-autocomplete-ajax** is released under the BSD 3-Clause License. See the bundled `LICENSE.md` for details.
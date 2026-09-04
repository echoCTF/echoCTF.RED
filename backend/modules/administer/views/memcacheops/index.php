<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
$this->title = 'Memcache Operations';
?>

<div class="memcacheops-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-6">
            <div class="input-group">
                <input type="text" id="key-input" class="form-control" placeholder="Enter cache key...">
                <span class="input-group-btn">
                    <button id="fetch-btn" class="btn btn-primary">Fetch</button>
                </span>
            </div>
        </div>
    </div>

    <div id="result-container" style="margin-top: 20px; display: none;">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>Key: <span id="display-key"></span></strong>
                <button id="delete-btn" class="btn btn-danger btn-xs pull-right" style="margin-top: -4px;">
                    Delete
                </button>
            </div>
            <div class="panel-body" id="display-value">
                <!-- Value will be inserted here -->
            </div>
        </div>
    </div>
</div>

<?php
$fetchUrl = Url::to(['fetch']);
$deleteUrl = Url::to(['delete']);
$csrfToken = Yii::$app->request->csrfToken;

$js = <<<JS
    $('#fetch-btn').on('click', function() {
        var key = $('#key-input').val().trim();
        if (!key) {
            alert('Please enter a cache key.');
            return;
        }

        $.get('$fetchUrl', { name: key }, function(response) {
            if (response.success) {
                $('#display-key').text(key);
                $('#display-value').text(response.value);
                $('#result-container').show();
                // Store the key for delete action
                $('#delete-btn').data('key', key);
            } else {
                alert('Error: ' + response.error);
                $('#result-container').hide();
            }
        }, 'json');
    });

    $('#delete-btn').on('click', function() {
        var key = $(this).data('key');
        if (!key) {
            return;
        }
        if (!confirm('Are you sure you want to delete key "' + key + '"?')) {
            return;
        }

        $.post('$deleteUrl', { name: key, _csrf: '$csrfToken' }, function(response) {
            // Since actionDelete redirects, we cannot rely on JSON response.
            // We'll just reload the page or hide the result.
            // Optionally, we can check response status by handling redirect.
            // For simplicity, we'll hide the container and show a message.
            $('#result-container').hide();
            alert('Key "' + key + '" deleted successfully.');
            // Or you could reload the page: location.reload();
        }).fail(function() {
            alert('Delete failed.');
        });
    });
JS;

$this->registerJs($js);
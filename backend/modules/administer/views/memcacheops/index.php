<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
$this->title = 'Memcache Operations';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];

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
                <button id="save-btn" class="btn btn-success btn-xs pull-right" style="margin-top: -4px; margin-right: 5px;">
                    Save
                </button>
            </div>
            <div class="panel-body">
                <div id="missing-note" class="alert alert-warning" style="display: none;"></div>
                <textarea id="display-value" class="form-control" rows="5" placeholder="Value will appear here..."></textarea>
                <div class="form-group" style="margin-top: 10px;">
                    <label for="ttl-input">TTL (seconds):</label>
                    <input type="number" id="ttl-input" class="form-control" value="0" style="width: 150px; display: inline-block;">
                    <small class="text-muted">(0 = unlimited)</small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$fetchUrl = Url::to(['fetch']);
$deleteUrl = Url::to(['delete']);
$saveUrl = Url::to(['save']);
$csrfToken = Yii::$app->request->csrfToken;

$js = <<<JS
    // Fetch button
    $('#fetch-btn').on('click', function() {
        var key = $('#key-input').val().trim();
        if (!key) {
            alert('Please enter a cache key.');
            return;
        }

        $.get('$fetchUrl', { name: key }, function(response) {
            if (response.success) {
                $('#display-key').text(key);
                $('#display-value').val(response.value);
                $('#result-container').show();

                // Show/hide the missing note
                if (response.exists === false) {
                    $('#missing-note').text(response.message || 'Key not found. You can create it by entering a value and saving.').show();
                } else {
                    $('#missing-note').hide();
                }

                $('#delete-btn').data('key', key);
                $('#save-btn').data('key', key);
            } else {
                alert('Error: ' + response.error);
                $('#result-container').hide();
            }
        }, 'json');
    });

    // Delete button
    $('#delete-btn').on('click', function() {
        var key = $(this).data('key');
        if (!key) return;
        if (!confirm('Are you sure you want to delete key "' + key + '"?')) return;

        var url = '$deleteUrl?name=' + encodeURIComponent(key);
        $.ajax({
            type: 'POST',
            url: url,
            headers: { 'X-CSRF-Token': '$csrfToken' },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#result-container').hide();
                    $('#key-input').val('');
                } else {
                    alert('Error: ' + response.error);
                }
            },
            error: function() {
                alert('Delete request failed.');
            }
        });
    });

    // Save button
    $('#save-btn').on('click', function() {
        var key = $(this).data('key');
        if (!key) return;
        var value = $('#display-value').val();
        var ttl = $('#ttl-input').val() || 0;

        $.ajax({
            type: 'POST',
            url: '$saveUrl',
            headers: { 'X-CSRF-Token': '$csrfToken' },
            data: {
                name: key,
                value: value,
                ttl: ttl
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    // After saving, we could refetch to confirm, but we'll just hide the missing note
                    $('#missing-note').hide();
                } else {
                    alert('Error: ' + response.error);
                }
            },
            error: function() {
                alert('Save request failed.');
            }
        });
    });
JS;

$this->registerJs($js);
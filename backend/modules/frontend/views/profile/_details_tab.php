<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;

use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;
?>
<div class="player-abuser-form">
  <h4>Report a player abuse</h4>
  <?php $form = ActiveForm::begin(['action' => ['/moderation/abuser/create'], 'id' => 'create-abuser', 'method' => 'post',]); ?>
  <div class="row d-flex">
    <div class="col">
      <?= $form->field($abuserModel, 'model_id')->widget(AutocompleteAjax::class, [
        'multiple' => false,
        'url' => ['/frontend/player/ajax-search'],
        'options' => ['placeholder' => 'Find player by email, username, id or profile.']
      ])->label(false) ?>
    </div>
    <div class="col"><?= $form->field($abuserModel, 'reason')
                        ->dropDownList(
                          ['fake_account' => 'Fake account'],           // Flat array ('id'=>'label')
                          ['prompt' => 'Choose a reason']    // options
                        )->label(false) ?>
    </div>
    <div class="col justify-content-center align-self-center">
      <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>
    <?= $form->field($abuserModel, 'player_id')->hiddenInput(['value' => $model->player_id])->label(false); ?>

    <?= $form->field($abuserModel, 'model')->hiddenInput(['value' => 'player'])->label(false); ?>
    <?php ActiveForm::end(); ?>
  </div>
</div>
<hr />
<?php if ($model->owner->wsToken): ?>
  <p><button id="wsMonitorBtn" class="btn btn-sm btn-outline-primary">Connect</button></p>
  <div id="wsMonitorModal" data-ws-url="<?= \Yii::$app->params['serverPublisher']['wsEndpoint'] . '?token=' . urlencode($model->owner->wsToken->token) ?>">
    <div id="ws-monitor-output"
      class="bg-dark text-light p-3 rounded"
      style="max-height: 400px; overflow-y: auto; font-family: monospace; font-size: 0.85rem;">
    </div>
  </div>
<hr />
<script>
  (function() {
    let ws = null;
    let connected = false;

    const container = document.getElementById('wsMonitorModal');
    const output = document.getElementById('ws-monitor-output');
    const btn = document.getElementById('wsMonitorBtn');

    btn.addEventListener('click', () => {
      if (connected) {
        disconnect();
      } else {
        connect();
      }
    });

    function connect() {
      if (ws) return;
      ws = new WebSocket(container.dataset.wsUrl);

      ws.onopen = () => {
        connected = true;
        btn.textContent = 'Disconnect';
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-outline-danger');
        appendLine(output, '[connected]');
      };

      ws.onmessage = (e) => appendLine(output, e.data);

      ws.onerror = () => appendLine(output, '[connection error]');

      ws.onclose = () => {
        appendLine(output, '[disconnected]');
        ws = null;
        connected = false;
        btn.textContent = 'Connect';
        btn.classList.remove('btn-outline-danger');
        btn.classList.add('btn-outline-primary');
      };
    }

    function disconnect() {
      if (ws) {
        ws.close();
      }
    }

    function appendLine(container, text) {
      const line = document.createElement('div');
      line.textContent = `[${new Date().toISOString()}] ${text}`;
      container.appendChild(line);
      container.scrollTop = container.scrollHeight;
    }
  })();
</script>
<?php endif; ?>
<?php if ($model->owner->targetInstances): ?>
  <div class="playerInstances">
    <h4>Player Instances</h4>
    <?= GridView::widget([
      'dataProvider' => new ArrayDataProvider([
        'id' => 'playerInstances',
        'allModels' => $model->owner->targetInstances,
        'sort' => [
          'sortParam' => 'playerInstance',
          'attributes' => ['player_id', 'target_id'],
        ],
        'pagination' => [
          'pageSize' => 20,
        ],
      ]),
      'columns' => [
        'name',
        [
          'attribute' => 'target.name',
          'label' => 'Target'
        ],
        [
          'attribute' => 'server.name',
          'label' => 'Server'
        ],
        [
          'attribute' => 'ipoctet',
          'label' => 'IP'
        ],
        [
          'attribute' => 'reboot',
          'label' => 'Status',
          'value' => 'rebootVal'
        ],
        'created_at',
        'updated_at'
      ],
    ]); ?>
  </div>
<?php endif; ?>


<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;

use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;
?>
<div class="player-abuser-form">
  <h4>Report a player abuse</h4>
  <?php $form = ActiveForm::begin(['action' =>['/moderation/abuser/create'], 'id' => 'create-abuser', 'method' => 'post',]); ?>
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
          ['fake_account'=>'Fake account'],           // Flat array ('id'=>'label')
          ['prompt'=>'Choose a reason']    // options
          )->label(false)?>
    </div>
    <div class="col justify-content-center align-self-center">
      <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>
    <?= $form->field($abuserModel, 'player_id')->hiddenInput(['value'=> $model->player_id])->label(false); ?>

    <?= $form->field($abuserModel, 'model')->hiddenInput(['value'=> 'player'])->label(false); ?>
    <?php ActiveForm::end(); ?>
  </div>
</div>
<hr />
<?php if($model->owner->targetInstances):?>
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
            'value'=>'rebootVal'
          ],
          'created_at',
          'updated_at'
        ],
      ]); ?>
</div>
<?php endif;?>
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\moderation\models\WriteupRelaySearch */
/* @var $action string the report action id */

$identity = $searchModel instanceof \app\modules\moderation\models\WriteupRelayIdentitySearch;
?>
<div class="card mb-3">
  <div class="card-body">
    <?php $form = ActiveForm::begin([
      'action' => [$action],
      'method' => 'get',
      'options' => ['data-pjax' => 1],
    ]); ?>
    <div class="row">
      <div class="col-md-3"><?= $form->field($searchModel, 'username') ?></div>
      <div class="col-md-3"><?= $form->field($searchModel, 'target') ?></div>
      <div class="col-md-2"><?= $form->field($searchModel, 'date_from')->input('date') ?></div>
      <div class="col-md-2"><?= $form->field($searchModel, 'fake_status')->dropDownList(\app\modules\frontend\models\Player::STATUSES, ['prompt' => 'Any']) ?></div>
      <div class="col-md-2"><?= $form->field($searchModel, 'beneficiary_status')->dropDownList(\app\modules\frontend\models\Player::STATUSES, ['prompt' => 'Any']) ?></div>
      <?php if (intval(Yii::$app->sys->academic_grouping) > 0) :
        $academics = [];
        for ($i = 0; $i < intval(Yii::$app->sys->academic_grouping); $i++) {
          $academics[$i] = Yii::$app->sys->{'academic_' . $i . 'short'};
        } ?>
        <div class="col-md-2"><?= $form->field($searchModel, 'fake_academic')->dropDownList($academics, ['prompt' => 'Any']) ?></div>
        <div class="col-md-2"><?= $form->field($searchModel, 'beneficiary_academic')->dropDownList($academics, ['prompt' => 'Any']) ?></div>
      <?php endif; ?>
      <?php if ($identity) : ?>
        <div class="col-md-1"><?= $form->field($searchModel, 'window_days')->input('number', ['min' => 1]) ?></div>
        <div class="col-md-1"><?= $form->field($searchModel, 'min_score')->input('number', ['min' => 0]) ?></div>
      <?php endif; ?>
      <div class="col-md-2 d-flex align-items-center">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary me-2']) ?>
        <?= Html::a('Reset', [$action], ['class' => 'btn btn-outline-secondary']) ?>
      </div>
    </div>
    <?php ActiveForm::end(); ?>
  </div>
</div>

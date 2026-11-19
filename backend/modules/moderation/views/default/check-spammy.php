<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \app\modules\frontend\models\Player;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\moderation\models\SpammyDomainSearch */

$this->title = ucfirst(Yii::$app->controller->module->id) . ' / ' . ucfirst(Yii::$app->controller->id) . ' / Check Spammy';
$this->params['breadcrumbs'][] = ['label' => ucfirst(Yii::$app->controller->module->id)];
$this->params['breadcrumbs'][] = ['label' => ucfirst(Yii::$app->controller->id), 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Check Spammy';

?>
<div class="spammy-domains">
  <h1>Check Spammy</h1>
  <p>Email domains that fail validation. Choose your filters and press Scan, the check does DNS lookups and can take a while.</p>

  <div class="card mb-3">
    <div class="card-body">
      <?php $form = ActiveForm::begin([
        'action' => ['check-spammy'],
        'method' => 'get',
      ]); ?>
      <div class="row">
        <div class="col-md-3"><?= $form->field($searchModel, 'status')->dropDownList(Player::STATUSES, ['prompt' => 'Any']) ?></div>
        <div class="col-md-2"><?= $form->field($searchModel, 'min_players')->input('number', ['min' => 0]) ?></div>
        <div class="col-md-5"><?= $form->field($searchModel, 'skip_domains')->textarea(['rows' => 1, 'placeholder' => 'comma or newline separated, e.g. gmail.com']) ?></div>
        <?php if (intval(Yii::$app->sys->academic_grouping) > 0) :
          $academics = [];
          for ($i = 0; $i < intval(Yii::$app->sys->academic_grouping); $i++) {
            $academics[$i] = Yii::$app->sys->{'academic_' . $i . 'short'};
          } ?>
          <div class="col-md-2"><?= $form->field($searchModel, 'academic')->dropDownList($academics, ['prompt' => 'Any']) ?></div>
        <?php endif; ?>
        <div class="col-md-2 d-flex align-items-center">
          <?= Html::submitButton('Scan', ['class' => 'btn btn-primary me-2']) ?>
          <?= Html::a('Reset', ['check-spammy'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
      </div>
      <?php ActiveForm::end(); ?>
    </div>
  </div>
</div>

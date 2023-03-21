<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use kartik\tabs\TabsX;
use yii\bootstrap5\Dropdown;
use yii\bootstrap5\ButtonDropdown;

/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\Profile */

$js = <<<SCRIPT
$(function () {
    $("[data-toggle='tooltip']").tooltip();
});;
$(function () {
    $("[data-toggle='popover']").popover();
});
SCRIPT;
// Register tooltip/popover initialization javascript
$this->registerJs($js);

$this->title = "Full view of " . Html::encode($model->name) . " with id: " . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Targets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<section class="container">
  <div class="profile-view-full">
    <!-- Begin .page-heading -->
    <p></p>
    <?= $this->render('full-view/_heading', ['model' => $model]); ?>
    <div class="row">
      <div class="col-md-4">
        <?= $this->render('full-view/_target_booleans', ['model' => $model]); ?>
        <?= $this->render('full-view/_target_details', ['model' => $model]); ?>
      </div>

      <div class="col-md-8">
        <div class="tab-block">
          <?php
          echo TabsX::widget([
            'position' => TabsX::POS_ABOVE,
            'align' => TabsX::ALIGN_LEFT,
            'encodeLabels'=>false,
            'pluginOptions' => [
              'enableCache' => false,
              'cacheTimeout' => 10000
            ],
            'items' => [
              [
                'label' => '<i class="fas fa-file-contract" data-toggle="tooltip" data-placement="top" title="Target Properties"></i>',
                'content' => $this->render('full-view/_properties-tab', ['model' => $model]),
                'headerOptions' => ['style' => 'font-weight:bold'],
                'options' => ['id' => '_properties-tab'],
                'active' => true,
              ],
              [
                'label' => '<i class="fas fa-spinner" data-placement="top" title="Player Progress"></i>',
                'headerOptions' => ['style' => 'font-weight:bold'],
                'linkOptions' => ['data-url' => Url::to(['player-progress', 'id' => $model->id])],
                'options' => ['id' => 'player-progress-tab'],
              ],
              [
                'label' => '<i class="fas fa-skull-crossbones" data-toggle="tooltip" data-placement="top" title="Headshots"></i>',
                'headerOptions' => ['style' => 'font-weight:bold'],
                'linkOptions' => ['data-url' => Url::to(['headshots', 'id' => $model->id])],
                'options' => ['id' => 'headshots-tab'],
              ],
              [
                'label' => '<i class="fas fa-book-dead" data-toggle="tooltip" data-placement="top" title="Submitted Writeups"></i>',
                'headerOptions' => ['style' => 'font-weight:bold'],
                'linkOptions' => ['data-url' => Url::to(['writeups', 'id' => $model->id])],
                'options' => ['id' => 'writeups-tab'],
              ],
              [
                'label' => '<i class="fas fa-notes-medical" data-toggle="tooltip" data-placement="top" title="Activated Writeups"></i>',
                'headerOptions' => ['style' => 'font-weight:bold'],
                'linkOptions' => ['data-url' => Url::to(['activated-writeups', 'id' => $model->id])],
                'options' => ['id' => 'activated-writeups-tab'],
              ],
              [
                'label' => '<i class="fas fa-sync" data-toggle="tooltip" data-placement="top" title="Spin History"></i>',
                'headerOptions' => ['style' => 'font-weight:bold'],
                'linkOptions' => ['data-url' => Url::to(['spin-history', 'id' => $model->id])],
                'options' => ['id' => 'spin-history-tab'],
              ],
            ],
          ]);
          ?>
        </div>
      </div>
    </div>
  </div>
</section>
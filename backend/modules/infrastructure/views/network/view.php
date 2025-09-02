<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap5\Modal;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\Network */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Networks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
Modal::begin([
  'id' => 'notify-modal',
  'title' => '<h2><i class="fas fa-paper-plane"></i> Notify players of [<code>' . Html::encode($this->title) . '</code>]</h2>',
  'toggleButton' => false,
  'options' => ['class' => 'modal-lg']
]);
echo '<div id="notificationContent"></div>'; // Content will be loaded here via AJAX
Modal::end();
?>
<div class="network-view">

  <h1><?= Html::encode($this->title) ?></h1>

  <p>
    <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
      'class' => 'btn btn-danger',
      'data' => [
        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
        'method' => 'post',
      ],
    ]) ?>
    <?= \app\widgets\NotifyButton::widget(['url' => ['notify', 'id' => $model->id],]) ?>
  </p>

  <?= DetailView::widget([
    'model' => $model,
    'attributes' => [
      'id',
      'name',
      'codename',
      'icon',
      'description:ntext',
      'public:boolean',
      'guest:boolean',
      'active:boolean',
      'announce:boolean',
      'weight:integer',
      'ts',
    ],
  ]) ?>

</div>

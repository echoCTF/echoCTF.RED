<?php

use yii\grid\GridView;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\settings\models\CreditsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Menu');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="menu-index">
  <h1><?= Html::encode($this->title) ?></h1>
  <p>

    <?= Html::a('Create Menu Item', ['create'], ['class' => 'btn btn-primary']); ?>
    <?= Html::a('Tree View', ['tree'], ['class' => 'btn btn-secondary']); ?>
  </p>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
      'id',
      'label',
      'url',
      'parent_id',
      'sort_order',
      'visibility',
      [
        'class' => 'app\components\columns\BooleanColumn',
        'attribute' => 'enabled',
        'contentOptions' => ['class' => 'text-center fs-5'],
        'filter' => ['1' => 'Enabled ', '0' => 'Disabled ']
      ],
      [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{toggle} {update} {delete}',
        'buttons' => [
          'toggle' => function ($url, $model, $key) {
            $icon = $model->enabled
              ? '<i class="fas fa-toggle-on"></i>'
              : '<i class="fas fa-toggle-off"></i>';
            return \yii\helpers\Html::a($icon, ['toggle', 'id' => $model->id], [
              'title' => 'Toggle',
              'data-method' => 'post', // ensures POST request
              'data-pjax' => '0',
            ]);
          },
        ],
      ],
    ],
  ]); ?>

</div>
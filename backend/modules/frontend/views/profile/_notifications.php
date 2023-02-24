<?php

use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
?>
<?php Pjax::begin(['id' => 'notificationsPJ', 'enablePushState' => false, 'enableReplaceState' => false,]); ?>
<h5>Notifications</h5>
<?= GridView::widget([
  'id' => 'notifications',
  'dataProvider' => $dataProvider,
  'filterModel' => $searchModel,
  'columns' => [

    'id',
    'category',
    'title',
    'body:html',
    'archived',
    'created_at',
    'updated_at',
    [
      'class' => 'yii\grid\ActionColumn',
      'template' => '{delete}',
      'urlCreator' => function ($action, $model, $key, $index) {
        return Url::to(['/activity/notification/' . $action, 'id' => $model->id]);
      }
    ],
  ],
]);
Pjax::end();

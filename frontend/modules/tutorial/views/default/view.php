<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ListView;
use yii\data\ActiveDataProvider;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Tutorial */
$this->title=Yii::$app->sys->event_name.' Tutorials / '.Html::encode($model->title).' (ID#'.$model->id.')';
$this->title=$model->title;
\yii\web\YiiAsset::register($this);
?>
<div class="tutorial-view">
  <div class="body-content">
    <div class="well">
      <h1><b><?= Html::encode($model->title).' (ID#'.$model->id.')'?></b></h1>
      <p><?=$model->description;?></p>
    </div>
<?php
  echo ListView::widget([
      'dataProvider' => $dataProvider,
      'summary'=>false,
      'itemOptions' => [
        'tag' => false
      ],
      'itemView' => '_task',
  ]);
?>

  </div>
</div>

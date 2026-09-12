<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ListView;
use yii\data\ActiveDataProvider;
use yii\bootstrap\ActiveForm;
use app\widgets\Twitter;

/* @var $this yii\web\View */
/* @var $model app\models\Tutorial */
$this->title=\Yii::t('app','{event_name} Tutorials / (ID#{id}) {title}', ['event_name'=>\Yii::$app->sys->event_name,'id'=>$model->id,'title'=>$model->title]);
$this->_description=\yii\helpers\StringHelper::truncateWords(strip_tags($model->description), 15);
$this->_url=\yii\helpers\Url::to(['view', 'id'=>$model->id], 'https');

?>
<div class="tutorial-view">
  <div class="body-content">
    <div class="well">
      <h2><b><?= Html::encode($model->title).' (ID#'.$model->id.')'?></b></h2>
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

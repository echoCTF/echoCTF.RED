<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ListView;
use yii\data\ActiveDataProvider;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Challenge */
$this->title = Yii::$app->sys->event_name .' Challenges / '.Html::encode($model->name).' (ID#'.$model->id.')';
$this->title = $model->name;
\yii\web\YiiAsset::register($this);
?>
<div class="challenge-view">
  <div class="body-content">
    <div class="well">
      <h1><b><?= Html::encode ( $model->name ) . ' (ID#'.$model->id.')'?></b></h1>
      <h4><b>Category:</b> <?=Html::encode($model->category);?></h4>
      <h4><b>Difficulty:</b> <?=Html::encode($model->difficulty)?></h4>
      <p><?=$model->description;?></p>
    </div>
<?php
  echo ListView::widget([
      'dataProvider' => $dataProvider,
      'summary'=>false,
      'itemOptions' => [
        'tag' => false
      ],
      'itemView' => '_question',
  ]);
?>

<?php $form = ActiveForm::begin([
    'enableClientValidation' => false,
    'id' => 'answer-form',
    'layout' => 'horizontal',
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
        'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
    ]); ?>
    <?= $form->field($answer, 'answer')->textInput(['autofocus' => true]) ?>
    <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'answer-button']) ?>
    <?php ActiveForm::end(); ?>
  </div>
</div>

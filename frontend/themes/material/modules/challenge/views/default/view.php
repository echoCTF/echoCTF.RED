<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ListView;
use yii\data\ActiveDataProvider;
use yii\bootstrap\ActiveForm;
use app\widgets\Twitter;
use app\widgets\solver\SolverWidget;
use app\widgets\vote\VoteWidget;

/* @var $this yii\web\View */
/* @var $model app\models\Challenge */
$this->title=Html::encode(Yii::$app->sys->event_name.' '.\Yii::t('app','Challenges').' / (ID#'.$model->id.') '.$model->name);
$this->_description=\yii\helpers\StringHelper::truncateWords(strip_tags($model->description), 15);
$this->_url=\yii\helpers\Url::to(['view', 'id'=>$model->id], 'https');
global $first;
$first=false;
?>
<div class="challenge-view">
  <div class="body-content">
    <div class="row">
      <div class="col-lg-8 col-md-8 col-sm-12">
        <div class="well">
          <h3><b><?=$model->name.' (ID#'.$model->id.')'?> <?php if($model->completed):?><i class="fas fa-check-double"></i> <?=Twitter::widget(['message'=>\Yii::t('app','Hey check this out, I completed the challenge ').Html::encode($model->name)]);?><?php else:?><?=Twitter::widget(['message'=>\Yii::t('app','I currently grinding the challenge ').Html::encode($model->name)]);?><?php endif;?></b></h3>
          <h4><?=Html::encode($model->category);?> / <?=Html::encode($model->difficulty)?> / <?=Html::encode(number_format($model->points));?>pts</h4>
          <?=trim($model->filename) !== '' ? '<h4><b>'.\Yii::t('app','Challenge file:').'</b> '.Html::a($model->filename, [Yii::getAlias(\Yii::$app->sys->challenge_root).'/'.$model->filename], ['data-pjax'=>"0"]).'</h4>' : ''?>
          <?php if($model->completed):?>
          <div class="col-xl-4 col-sm-6"><?=VoteWidget::widget(['model'=>$solver,'id'=>$model->id,'action'=>['/game/default/rate-solver','id'=>$model->id]]);?></div>
          <?php endif;?>
          <p><?=$model->description;?></p>
        </div>
        <div id="accordion">
        <?php
          echo ListView::widget([
              'dataProvider' => $dataProvider,
              'emptyText'=>'<b class="text-danger">No questions found for the challenge. You will have to wait for the administrators to add a question.</b>',
              'summary'=>false,
              'itemOptions' => [
                'tag' => false
              ],
              'itemView' => '_question',
              'viewParams'=>['answer'=>$answer],
          ]);
        ?>
        </div>

  </div>
  <div class="col-lg-4 col-md-4 col-sm-12">
    <?= SolverWidget::widget(['model' => $solvers,'slice'=>30]) ?>
  </div>

  </div>
</div>
</div>
<?php
unset($first);

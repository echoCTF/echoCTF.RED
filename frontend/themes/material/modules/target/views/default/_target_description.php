<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\modules\target\models\PlayerTargetHelp as PTH;
?>
<div class="card terminal">
  <div class="card-body">
    <?php if(!Yii::$app->user->isGuest && count($target->writeups)>0 && PTH::findOne(['player_id'=>Yii::$app->user->id,'target_id'=>$target->id])===null && !($identity->player_id===Yii::$app->user->id && $target->progress==100)):?>
    <?=Html::a(
      '<i class="fas fa-question-circle" style="font-size: 1.5em;"></i> '.\Yii::t('app','Writeups available.'),
        ['/target/writeup/enable', 'id'=>$target->id],
        [
          'style'=>"font-size: 1.0em;",
          'title' => \Yii::t('app','Request access to writeups'),
          'rel'=>"tooltip",
          'data-pjax' => '0',
          'data-method' => 'POST',
          'data-confirm'=>\Yii::t('app','Are you sure you want to enable access to writeups for this target? Any remaining flags will have their points reduced by 50%.'),
          'aria-label'=>\Yii::t('app','Request access to writeups'),
        ]
    )?><br/><?php endif;?>
    <?=$target->description?>
    <?=$this->render('_target_metadata',['target'=>$target,'identity'=>$identity]);?>
    <?=$this->render('_target_migration_schedule',['scheduled'=>$target->scheduled,'network'=>$target->network]);?>
  </div>
</div>

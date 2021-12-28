<?php
use app\modules\target\models\PlayerTargetHelp as PTH;
?>
<div class="card terminal">
  <div class="card-body">
    <?php if(!Yii::$app->user->isGuest && count($target->writeups)>0 && PTH::findOne(['player_id'=>Yii::$app->user->id,'target_id'=>$target->id])===null && !($identity->player_id===Yii::$app->user->id && $target->progress==100)):?>
    <?=Html::a(
      '<i class="fas fa-question-circle" style="font-size: 1.5em;"></i> Writeups available.',
        ['/target/writeup/enable', 'id'=>$target->id],
        [
          'style'=>"font-size: 1.0em;",
          'title' => 'Request access to writeups',
          'rel'=>"tooltip",
          'data-pjax' => '0',
          'data-method' => 'POST',
          'data-confirm'=>'Are you sure you want to enable access to writeups for this target? Any remaining flags will have their points reduced by 50%.',
          'aria-label'=>'Request access to writeups',
        ]
    )?><br/><?php endif;?>
    <?=$target->description?>
    <?=$this->render('_target_metadata',['target'=>$target,'identity'=>$identity]);?>
    <?php if(!Yii::$app->user->isGuest && Yii::$app->user->id === $identity->player_id):?>
      <?php if(Yii::$app->user->identity->getPlayerHintsForTarget($target->id)->count() > 0) echo "<br/><i class='fas fa-lightbulb text-success'></i> <code class='text-success'>", implode(', ', ArrayHelper::getColumn($identity->owner->getPlayerHintsForTarget($target->id)->all(), 'hint.title')), "</code>";?>
    <?php endif;?>
  </div>
</div>

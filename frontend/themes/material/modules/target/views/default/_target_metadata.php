<?php if(!Yii::$app->user->isGuest && $target->metadata):?>
  <?php if(Yii::$app->user->identity->isAdmin && !empty($target->metadata->scenario)):?><b><?=\Yii::t('app','Scenario')?></b>: <?=$target->metadata->scenario?><br/><?php endif;?>
  <?php if(Yii::$app->user->identity->isAdmin && !empty($target->metadata->instructions)):?><b><?=\Yii::t('app','Instructions')?></b>: <?=$target->metadata->instructions?><br/><?php endif;?>
  <?php if(Yii::$app->user->identity->isAdmin && !empty($target->metadata->solution)):?><b><?=\Yii::t('app','Solution')?></b>: <?=$target->metadata->solution?><br/><?php endif;?>
  <?php if(!empty($target->metadata->pre_credits)):?><b><?=\Yii::t('app','Pre exploitation credits')?></b>: <?=$target->metadata->pre_credits?><br/><?php endif;?>
  <?php if(!empty($target->metadata->pre_exploitation)):?><b><?=\Yii::t('app','Pre exploitation details')?></b>: <?=$target->metadata->pre_exploitation?><br/><?php endif;?>
  <?php if((($identity->player_id===Yii::$app->user->id && $target->progress==100) || Yii::$app->user->identity->isAdmin) && !empty($target->metadata->post_exploitation)):?><b><?=\Yii::t('app','Post exploitation')?></b>: <?=$target->metadata->post_exploitation?><br/><?php endif;?>
  <?php if((($identity->player_id===Yii::$app->user->id && $target->progress==100) || Yii::$app->user->identity->isAdmin) && !empty($target->metadata->post_exploitation)):?><b><?=\Yii::t('app','Post exploitation credits')?></b>: <?=$target->metadata->post_credits?><br/><?php endif;?>
<?php endif;?>

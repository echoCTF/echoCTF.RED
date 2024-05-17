<?php if(!Yii::$app->user->isGuest && $target->metadata):?>
  <?php if(Yii::$app->user->identity->isAdmin && !empty($target->metadata->scenario)):?><b><?=\Yii::t('app','Scenario')?></b>: <?=\yii\helpers\Markdown::process($target->metadata->scenario,'gfm')?><br/><?php endif;?>
  <?php if(Yii::$app->user->identity->isAdmin && !empty($target->metadata->instructions)):?><b><?=\Yii::t('app','Instructions')?></b>: <?=\yii\helpers\Markdown::process($target->metadata->instructions,'gfm')?><br/><?php endif;?>
  <?php if(Yii::$app->user->identity->isAdmin && !empty($target->metadata->solution)):?><b><?=\Yii::t('app','Solution')?></b>: <?=\yii\helpers\Markdown::process($target->metadata->solution,'gfm')?><br/><?php endif;?>
  <?php if(!empty($target->metadata->pre_credits)):?><b><?=\Yii::t('app','Pre exploitation credits')?></b>: <?=\yii\helpers\Markdown::process($target->metadata->pre_credits,'gfm')?><br/><?php endif;?>
  <?php if(!empty($target->metadata->pre_exploitation)):?><b><?=\Yii::t('app','Pre exploitation details')?></b>: <?=\yii\helpers\Markdown::process($target->metadata->pre_exploitation,'gfm')?><br/><?php endif;?>
  <?php if((($identity->player_id===Yii::$app->user->id && $target->progress==100) || Yii::$app->user->identity->isAdmin) && !empty($target->metadata->post_exploitation)):?><b><?=\Yii::t('app','Post exploitation')?></b>: <?=\yii\helpers\Markdown::process($target->metadata->post_exploitation,'gfm')?><br/><?php endif;?>
  <?php if((($identity->player_id===Yii::$app->user->id && $target->progress==100) || Yii::$app->user->identity->isAdmin) && !empty($target->metadata->post_credits)):?><b><?=\Yii::t('app','Post exploitation credits')?></b>: <?=\yii\helpers\Markdown::process($target->metadata->post_credits,'gfm')?><br/><?php endif;?>
<?php endif;?>

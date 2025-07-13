<?php if(!Yii::$app->user->isGuest && $metadata):?>
  <?php if(Yii::$app->user->identity->isAdmin):?>
    <?php if(!empty($metadata->scenario)):?><b><?=\Yii::t('app','Scenario')?></b>: <?=\yii\helpers\Markdown::process($metadata->scenario,'gfm')?><br/><?php endif;?>
    <?php if(!empty($metadata->instructions)):?><b><?=\Yii::t('app','Instructions')?></b>: <?=\yii\helpers\Markdown::process($metadata->instructions,'gfm')?><br/><?php endif;?>
    <?php if(!empty($metadata->solution)):?><b><?=\Yii::t('app','Solution')?></b>: <?=\yii\helpers\Markdown::process($metadata->solution,'gfm')?><br/><?php endif;?>
  <?php endif;?>
  <?php if(!empty($metadata->pre_credits)):?><b><?=\Yii::t('app','Pre exploitation credits')?></b>: <?=\yii\helpers\Markdown::process($metadata->pre_credits,'gfm')?><br/><?php endif;?>
  <?php if(!empty($metadata->pre_exploitation)):?><b><?=\Yii::t('app','Pre exploitation details')?></b>: <?=\yii\helpers\Markdown::process($metadata->pre_exploitation,'gfm')?><br/><?php endif;?>
  <?php if((($identity->player_id===Yii::$app->user->id && $target->progress==100) || Yii::$app->user->identity->isAdmin) && !empty($metadata->post_exploitation)):?><b><?=\Yii::t('app','Post exploitation')?></b>: <?=\yii\helpers\Markdown::process($metadata->post_exploitation,'gfm')?><br/><?php endif;?>
  <?php if((($identity->player_id===Yii::$app->user->id && $target->progress==100) || Yii::$app->user->identity->isAdmin) && !empty($metadata->post_credits)):?><b><?=\Yii::t('app','Post exploitation credits')?></b>: <?=\yii\helpers\Markdown::process($metadata->post_credit,'gfm')?><br/><?php endif;?>
<?php endif;?>

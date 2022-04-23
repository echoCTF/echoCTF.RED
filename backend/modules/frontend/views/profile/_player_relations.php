<?php
use yii\helpers\Html;
use app\modules\frontend\models\PlayerRelation as PR;
?>
<div class="panel">
  <div class="panel-heading">
    <span class="panel-icon">
      <i class="far fa-id-badge"></i>
    </span>
    <span class="panel-title"> Relations</span>
  </div>
  <div class="panel-body pb5">
    <?php foreach(PR::find()->where(['OR',['=','player_id',$model->player_id],['=','referred_id',$model->player_id]])->all() as $ref):?>
    <?php if($ref->player_id===$model->player_id):?>
      <span class="label label-success mr5 mb10 ib lh15"><i class="fas fa-child"></i> <?=Html::encode($ref->referred ? $ref->referred->username: "user deleted")?></span>
    <?php else:?>
      <span class="label label-warning mr5 mb10 ib lh15"><i class="fas fa-brain"></i> <?=Html::encode($ref->player->username)?></span>
    <?php endif;?>
    <?php endforeach;?>
  </div>
</div>

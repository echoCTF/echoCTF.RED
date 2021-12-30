<?php
use yii\helpers\Html;
?>
<div class="card card-profile">
  <div class="card-avatar bg-primary">
      <img class="img" src="/images/avatars/team/<?=$model->validLogo?>" />
  </div>

  <div class="card-body">
    <h6 class="badge badge-secondary"><?=$model->score !== null ? number_format($model->score->points) : 0?> points</h6>
    <h4 class="card-title"><?=Html::encode($model->name)?></h4>

    <p class="card-description">
      <?=Html::encode($model->description)?>
    </p>
    <ul class="nav flex-column">
      <li class="nav-item text-left"><strong><i class="fas fa-user-secret text-danger"></i> Owner</strong> <span class="pull-right"><?=$model->owner->profile->link?></span></li>
      <?php foreach($model->teamPlayers as $player):?>
        <?php if($player->player_id !== $model->owner_id):?>
        <li class="nav-item text-left"><strong><i class="fas fa-user-ninja <?=$player->approved===0 ? "text-info": "text-primary"?>"></i></strong> <span class="pull-right"><?=$player->player->profile->link?></span></li>
        <?php endif;?>
      <?php endforeach;?>
    </ul>
    <hr/>
    <div class="row">
    <div class="col"><?= Html::a('View', ['/team/default/view','token' => $model->token],['class'=>'btn btn-info d-block']) ?></div>
    <?php if($model->getTeamPlayers()->count()<Yii::$app->sys->members_per_team && !Yii::$app->user->identity->team):?>
    <div class="col"><?= Html::a('Join', ['/team/default/join','token' => $model->token],['class'=>'btn btn-primary d-block', 'data-method' => 'POST']) ?></div>
    <?php endif;?>
    </div>
  </div>
</div>

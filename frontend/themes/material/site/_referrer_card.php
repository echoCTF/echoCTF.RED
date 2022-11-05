<?php
use yii\helpers\Html;
?>
<?php if($referred!==false):?>
    <div class="col-lg-3">
      <div class="card card-profile">
        <div class="card-avatar bg-primary">
          <a href="<?=$referred->profile->linkTo?>">
            <img class="img" src="/images/avatars/<?=$referred->profile->avtr?>" />
          </a>
        </div>
        <div class="card-body">
          <h6 class="badge badge-secondary">Level <?=$referred->profile->experience->id?> / <?=$referred->profile->experience->name?></h6>
          <h4 class="card-title"><?=Html::encode($referred->profile->owner->username)?></h4>
          <p class="card-description">
            <?=\Yii::t('app','Hi and Welcome. {username} invites to you join the fun and hack some systems together.',['username'=>Html::encode($referred->username)])?>
          </p>
        </div>
      </div>
    </div>
<?php endif;?>

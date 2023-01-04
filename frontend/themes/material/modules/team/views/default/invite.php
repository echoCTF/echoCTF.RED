<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ListView;
$this->loadLayoutOverrides=true;

$this->title=Yii::$app->sys->event_name.' '.\Yii::t('app','Invite to join team').' ['.$team->name.']';

?>
<div class="team-invite">
  <div class="body-content text-center">
    <h2><?=\Yii::t('app','You have been invited to join')?> <code class="orbitron"><?=Html::encode($team->name)?></code></h2>
    <p class="lead text-bold orbitron"><?=Html::encode($team->recruitment)?></p>
    <hr />
    <div class="row d-flex justify-content-center">
      <div class="col-md-6">
        <?= $this->render('_team_card',['model'=>$team,'invite'=>true]);?>
      </div>
    </div>
  </div>
</div>

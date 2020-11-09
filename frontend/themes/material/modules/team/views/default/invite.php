<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ListView;

$this->title=Yii::$app->sys->event_name.' Invite to team ['.Html::encode($team->name).']';

?>
<div class="team-invite">
  <div class="body-content">
    <h2>You have been invited to join [<code><?=Html::encode($team->name)?></code>]</h2>
    <hr />
    <div class="row d-flex justify-content-center">
      <div class="col-md-6">
        <?= $this->render('_team_card',['model'=>$team]);?>
      </div>
    </div>
  </div>
</div>

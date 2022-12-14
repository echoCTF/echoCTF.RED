<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

$dataProvider=new ArrayDataProvider([
  'allModels' => $model->teamPlayers,
  'sort' => false,
  'pagination' => false,
]);

?>
<div class="card bg-dark">
  <div class="card-profile team-card<?= $model->inviteonly && !$invite ? " bg-dark" : "" ?>" style="height: 100%;">
    <!--<div class="card-header"></div>-->
    <div class="card-avatar bg-dark">
        <img class="img" src="/images/avatars/team/<?= $model->validLogo ?>" />
    </div>

    <div class="card-body">
      <h4 class="card-title <?= $model->inviteonly && !$invite ?"text-danger" : ""  ?>"><?= Html::encode($model->name) ?></h4>
      <h6 class="badge badge-secondary"><?= \Yii::t('app', '{points,plural,=0{0 points} =1{# point} other{# points}}', ['points' => $model->score !== null ? $model->score->points : 0]) ?></h6>
      <?php if ($model->inviteonly) : ?><h5 class="badge badge-primary"><?= \Yii::t('app', 'invite only') ?></h5><?php endif; ?>
      <p class="card-description"><?= Html::encode($model->description) ?></p>
      <?php
      echo GridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($model) {
          if ($model->approved !== 1) {
            return ['class' => 'bg-dark text-primary'];
          }
        },
        'tableOptions' => ['class' => 'table table-xl orbitron'],
        'layout' => '{items}',
        'summary' => '',
        'showHeader' => false,
        'columns' => [
          [
            'headerOptions' => ['style' => 'max-width: 35px',],
            'label' => null,
            'format' => 'raw',
            'value' => function ($model) {
              return Html::img('/images/avatars/' . $model->player->profile->avtr, ['class' => 'rounded', 'style' => 'max-width: 30px; max-height: 30px;']);
            }
          ],
          [
            'attribute' => 'player.username',
            'format' => 'raw',
            'value' => function ($model) {
              return $model->player->profile->link;
            },
            'label' => 'Member'
          ],
          [
            'headerOptions' => ['class' => 'd-none d-xl-table-cell',],
            'contentOptions' => ['class' => 'd-none d-md-table-cell d-lg-table-cell d-xl-table-cell', 'style' => 'width: 100%; text-align: right'],
            'format' => 'integer',
            'attribute' => 'player.playerScore.points',
          ],
          [
            'headerOptions' => ['class' => 'd-none d-sm-table-cell d-xl-table-cell', 'style' => "width: 1.5em"],
            'contentOptions' => ['class' => 'd-none d-sm-table-cell d-xl-table-cell', 'style' => "width: 1.5em;text-align: right"],
            'format' => 'raw',
            'value' => function ($model) {
              if ($model->approved)
                return '<i class="fas fa-check-square text-primary" style="font-size: 1.2em"></i>';
              return '<i class="fas fa-window-close text-danger" style="font-size: 1em"></i>';
            }
          ]
        ]
      ]);
      ?>
    </div>
    <div class="card-footer justify-content-center">
      <div class="row" style="position: absolute; bottom: 1em; margin-left: auto;margin-right: auto">
        <?php if (Yii::$app->user->identity->team && Yii::$app->user->identity->team->id === $model->id) : ?>
          <div class="col" style="margin-top: auto;"><?= Html::a('View', ['/team/default/view', 'token' => $model->token], ['class' => 'btn text-dark text-bold d-block' . (!$model->inviteonly ? ' btn-info' : ' btn-warning')]) ?></div>
        <?php endif; ?>
        <?php if ($model->getTeamPlayers()->count() < Yii::$app->sys->members_per_team && !Yii::$app->user->identity->team && (!$model->inviteonly || $invite)) : ?>
          <div class="col" style="margin-top: auto;"><?= Html::a('Join', ['/team/default/join', 'token' => $model->token], ['class' => 'btn btn-primary d-block text-dark text-bold', 'data-method' => 'POST', 'data' => ['confirm' => 'You are about to join this team. Your membership will have to be confirmed by the team captain.', 'method' => 'POST']]) ?></div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
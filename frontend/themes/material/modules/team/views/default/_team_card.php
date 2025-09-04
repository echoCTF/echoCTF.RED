<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

$dataProvider = new ArrayDataProvider([
  'allModels' => $model->teamPlayers,
  'sort' => false,
  'pagination' => false,
]);

?>
<div class="card card-profile team-card bg-dark">
  <div class="card-icon bg-dark">
    <img class="img rounded-circle bg-dark" src="/images/avatars/team/<?= $model->validLogo ?>" height="100vw" style="max-height: 100vw" />
  </div>

  <div class="card-body">
    <h4 class="card-title <?= $model->inviteonly && !$invite ? "text-danger" : ""  ?>"><?= Html::encode($model->name) ?></h4>
    <h6 class="badge badge-secondary"><?= \Yii::t('app', '{points,plural,=0{0 points} =1{# point} other{# points}}', ['points' => $model->score !== null ? $model->score->points : 0]) ?></h6>
    <?php if ($model->inviteonly) : ?><h5 class="badge badge-warning"><?= \Yii::t('app', 'invite only') ?></h5><?php endif; ?>
    <p class="card-description"><?= Html::encode($model->description) ?></p>
    <?php if ($listing === true): ?>
      <p class=""><b>Owner: <?= $model->owner->profile->getLink(true); ?></b></p>
    <?php else: ?>
      <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($model) {
          if ($model->approved !== 1) {
            return ['class' => 'bg-dark text-danger'];
          }
          if ($model->team->owner_id == $model->player_id) {
            return ['class' => 'text-bold teamLeader'];
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
              return $model->player->profile->getLink(true);
            },
            'label' => 'Member'
          ],
          [
            'headerOptions' => ['class' => 'd-none d-xl-table-cell',],
            'contentOptions' => ['class' => 'd-none d-md-table-cell d-lg-table-cell d-xl-table-cell', 'style' => 'width: 100%; text-align: right'],
            'value' => function ($model) {
              if($model->approved)
                return $model->player->teamStreamPoints->points ?? 0;
              return $model->player->playerScore->points ?? 0;
            },
            'format' => 'integer',
            'attribute' => 'player.teamStreamPoints.points',
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
    <?php endif; ?>

  </div>
  <div class="card-footer">
    <p class="small">
      <center>
        <?php if (Yii::$app->user->identity->isAdmin || (Yii::$app->user->identity->team && !$model->inviteonly || ($invite === false && $listing === true))) : ?>
          <?= Html::a('View Team', ['/team/default/view', 'token' => $model->token], ['class' => 'btn block text-dark text-bold orbitron' . (!$model->inviteonly ? ' btn-info' : ' btn-warning')]) ?>
        <?php elseif (intval($model->getTeamPlayers()->count()) < Yii::$app->sys->members_per_team && !Yii::$app->user->identity->team && !$model->locked && $model->invite) : ?>
          <?= Html::a('Join Team', ['/team/default/join', 'token' => $model->invite->token], ['class' => 'btn block btn-primary text-dark text-bold orbitron', 'data-method' => 'POST', 'data' => ['confirm' => 'You are about to join this team. Your membership will have to be confirmed by the team captain.', 'method' => 'POST']]) ?>
        <?php endif; ?></center>
    </p>
  </div>
</div>
<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\Team */
use yii\widgets\ActiveForm;
use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;
use yii\bootstrap5\Modal;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ucfirst(Yii::$app->controller->module->id);
$this->params['breadcrumbs'][] = ['label' => 'Teams', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
Yii::$app->user->setReturnUrl(['frontend/team/view', 'id' => $model->id]);
$this->params['jumpto'] = \app\widgets\JumpToWidget::widget([
    'name' => 'team_jump',
    'placeholder' => '🔍 Jump to team...',
    'sourceUrl' => ['/frontend/team/ajax-search'],
    'redirectUrl' => ['/frontend/team/view'],
    'idField' => 'id',
]);

?>
<div class="team-view" id="team-view">

  <h1><?= Html::encode($this->title) ?></h1>

  <p>
    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Delete', ['delete', 'id' => $model->id], [
      'class' => 'btn btn-danger',
      'data' => [
        'confirm' => 'Are you sure you want to delete this item?',
        'method' => 'post',
      ],
    ]) ?>
    <?= Html::a('Repopulate stream', ['repopulate-stream', 'id' => $model->id], [
      'class' => 'btn btn-warning',
      'data' => [
        'confirm' => 'Are you sure you want to repopulate the stream of this team?',
        'method' => 'post',
      ],
    ]) ?>
    <?= \app\widgets\NotifyButton::widget(['url' => ['notify', 'id' => $model->id],]) ?>

  </p>

  <div class="row">
    <div class="col-sm-2">
      <a href="//<?= Yii::$app->sys->offense_domain ?>/team/<?= $model->token ?>" target="_blank">
        <?php if ($model->logo) : ?>
          <img width="140px" height="140px" class="rounded-circle bg-dark shadow" src="//<?= Yii::$app->sys->offense_domain ?>/images/avatars/team/<?= $model->logo ?>" alt="<?= Yii::$app->sys->offense_domain ?>/images/avatars/<?= $model->logo ?>">
        <?php else : ?>
          <img width="140px" height="140px" class="rounded-circle bg-dark shadow" src="//<?= Yii::$app->sys->offense_domain ?>/images/team_player.png" alt="<?= Yii::$app->sys->offense_domain ?>/images/avatars/<?= $model->logo ?>">
        <?php endif; ?>
      </a>
    </div>
    <div class="col-sm-5">
      <h2 class="media-heading">(id: <?= $model->id ?>) <?= Html::encode($model->name) ?>
        <small> - <?= Html::encode($model->owner->username) ?> (owner_id: <?= $model->owner_id ?>)</small>
      </h2>
      <p style="font-weight: 800">ranked <?= $model->rank->ordinalPlace ?> with <?= number_format($model->score->points) ?> points</p>
      <p>
        <?php if ($model->invite): ?>
          <b>invite url: <?= Html::a(Url::to('//' . Yii::$app->sys->offense_domain . '/team/invite/' . $model->invite->token, 'https'), Url::to('//' . Yii::$app->sys->offense_domain . '/team/invite/' . $model->invite->token, 'https'), ['target' => '_blank']); ?></b><br />
        <?php endif; ?>
        <b>Team url: <?= Html::a(Url::to('//' . Yii::$app->sys->offense_domain . '/team/' . $model->token, 'https'), Url::to('//' . Yii::$app->sys->offense_domain . '/team/' . $model->token, 'https'), ['target' => '_blank']); ?></b><br />
        <b>description:</b> <?= Html::encode($model->description) ?><br />
        <b>recruitment:</b> <?= Html::encode($model->recruitment) ?><br />
        <b>academic:</b> <?= Html::encode($model->academicLong) ?><br />
        <b>inviteonly:</b> <?= Html::encode($model->inviteonly === 0 ? 'nop' : 'yep') ?><br />
        <b>locked:</b> <?= Html::encode($model->locked === 0 ? 'nop' : 'yep') ?><br />
        <b>last update:</b> <?= $model->ts ?>
        <?php $form = ActiveForm::begin(); ?>
      <div class="row d-flex align-items-center">
        <div class="col-md-10">
          <?= $form->field($newTP, 'player_id')->widget(AutocompleteAjax::class, [
            'multiple' => false,
            'url' => ['/frontend/team/free-player-ajax-search'],
            'options' => ['placeholder' => 'Add new player by email, username, id or profile.']
          ])->Label(false) ?>
        </div>
        <div class="col-md-2"><?= Html::submitButton('Add', ['class' => 'btn btn-success']) ?></div>
      </div>
      <?php ActiveForm::end(); ?>
      </p>
    </div>
  </div>
  <div class="row">
    <div class="col-6">
      <h4><b>Team Members</b></h4>
      <?php
      $dataProvider->setSort([
        'sortParam' => 'teamMembers',
        'defaultOrder' => ['ts' => SORT_ASC]
      ]);
      echo GridView::widget([
        'id' => 'teamMembers',
        'dataProvider' => $dataProvider,
        'columns' => [
          ['class' => 'app\components\columns\ProfileColumn', 'attribute' => 'player'],
          [
            'attribute' => 'player.playerLast.vpn_remote_address_octet',
            'label' => 'Remote IP'
          ],
          [
            'attribute' => 'player.playerLast.vpn_local_address_octet',
            'label' => 'Local IP'
          ],
          'approved:boolean',
          'ts',
          [
            //'class' => '\app\components\columns\ActionColumn',
            'class' => 'yii\grid\ActionColumn',
            'urlCreator' => function ($action, $model, $key, $index) {
              return Url::to(['teamplayer/' . $action, 'id' => $key]);
            },
            'template' => '{toggle-approved} {view} {update} {delete}',
            'buttons' => [
              'toggle-approved' => function ($url) {
                return Html::a(
                  '<i class="bi bi-check-circle-fill"></i>',
                  $url,
                  [
                    'title' => 'Toggle membership approved flag',
                    'data-pjax' => '0',
                    'data-method' => 'POST',
                    'data' => ['confirm' => 'Are you sure you want to toggle the approved flag for this user?']

                  ]
                );
              },
            ]
          ],
        ],
      ]); ?>
    </div>
    <div class="col-md-6">
      <h4><b>Team Instances</b></h4>
      <?= GridView::widget([
        'dataProvider' => new ArrayDataProvider([
          'id' => 'teamInstances',
          'allModels' => $model->instances,
          'sort' => [
            'sortParam' => 'teamInstance',
            'attributes' => ['player_id', 'target_id'],
          ],
          'pagination' => [
            'pageSize' => 20,
          ],
        ]),
        'columns' => [
          'name',
          ['class' => 'app\components\columns\ProfileColumn', 'attribute' => 'player'],
          [
            'attribute' => 'target.name',
            'label' => 'Target'
          ],
          [
            'attribute' => 'server.name',
            'label' => 'Server'
          ],
          [
            'attribute' => 'ipoctet',
            'label' => 'IP'
          ],
          [
            'attribute' => 'reboot',
            'label' => 'Status',
            'value' => 'rebootVal'
          ],
        ],
      ]); ?>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6">
      <h4><b>Points Team Stream</b></h4>
      <?= GridView::widget([
        'id' => 'teamStream',
        'dataProvider' => new ArrayDataProvider([
          'allModels' => $model->streams,
          'sort' => [
            'sortParam' => 'teamStream',
            'attributes' => ['model', 'model_id', 'points', 'ts','stream_id'],
            'defaultOrder' => ['stream_id'=>SORT_DESC,'ts' => SORT_DESC]
          ],
          'pagination' => [
            'pageSize' => 10,
          ],
        ]),
        'columns' => [
          [
            'attribute'=>'formatted',
            'format'=>'html',
            'value'=>function($model){ return sprintf('%s %s <abbr title="%s">%s</abbr>',$model->player->username,$model->formatted,$model->ts,$model->ts_ago);}
          ],
        ],
      ]); ?>
    </div>
    <div class="col-md-6">
      <h4><b>Team Headshots</b></h4>
      <?= GridView::widget([
        'id' => 'teamHeadshots',
        'dataProvider' => new ArrayDataProvider([
          'allModels' => array_filter($model->streams, function ($item) {
            return isset($item['model']) && $item['model'] === 'headshot';
          }),
          'sort' => [
            'sortParam' => 'teamHeadshots',
            'attributes' => ['model', 'model_id', 'points', 'ts'],
            'defaultOrder' => ['ts' => SORT_DESC]
          ],
          'pagination' => [
            'pageSize' => 10,
          ],
        ]),
        'columns' => [
          [
            'attribute'=>'headshotMessage',
            'format'=>'html'
          ],
          'ts'
        ],
      ]); ?>
    </div>
  </div>
</div>
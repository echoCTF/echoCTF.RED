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

$this->title = $model->name;
$this->params['breadcrumbs'][] = ucfirst(Yii::$app->controller->module->id);
$this->params['breadcrumbs'][] = ['label' => 'Teams', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
Yii::$app->user->setReturnUrl(['frontend/team/view', 'id' => $model->id]);
?>
<div class="team-view">

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
    <div class="col-sm-10">
      <h2 class="media-heading">(id: <?= $model->id ?>) <?= Html::encode($model->name) ?>
        <small> - <?= Html::encode($model->owner->username) ?> (owner_id: <?= $model->owner_id ?>)</small>
      </h2>
      <p style="font-weight: 800">ranked <?= $model->rank->ordinalPlace ?> with <?= number_format($model->score->points) ?> points</p>
      <p>
        <b>invite url: <?= Html::a(Url::to('//' . Yii::$app->sys->offense_domain . '/team/invite/' . $model->token, 'https'), Url::to('//' . Yii::$app->sys->offense_domain . '/team/invite/' . $model->token, 'https'), ['target' => '_blank']); ?></b><br />
        <b>description:</b> <?= Html::encode($model->description) ?><br />
        <b>recruitment:</b> <?= Html::encode($model->recruitment) ?><br />
        <b>academic:</b> <?= Html::encode($model->academicLong) ?><br />
        <b>inviteonly:</b> <?= Html::encode($model->inviteonly === 0 ? 'nop' : 'yep') ?><br />
        <b>locked:</b> <?= Html::encode($model->locked === 0 ? 'nop' : 'yep') ?><br />
        <b>last update:</b> <?= $model->ts ?>
        <?php $form = ActiveForm::begin(); ?>
        <div class="row d-flex align-items-center">
          <div class="col-md-4">
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
    <div class="col-sm-6 col-md-offset-2">
      <h4><b>Team Members</b></h4>
      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
          [
            'attribute' => 'player',
            'label' => 'Player',
            'value' => function ($model) {
              return sprintf("id:%d %s", $model->player_id, $model->player->username);
            },
          ],
          'approved:boolean',
          'ts',
          [
            'class' => 'yii\grid\ActionColumn',
            'urlCreator' => function ($action, $model, $key, $index) {
              return Url::to(['teamplayer/' . $action, 'id' => $key]);
            },
            'template' => '{toggle-approved} ' . '{view} {update} {delete}',
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
  </div>
  <div class="col-md-12">
    <?= GridView::widget([
      'dataProvider' => new ArrayDataProvider([
        'allModels' => $model->streams,
        'sort' => [
          'attributes' => ['model', 'model_id', 'points', 'ts'],
        ],
        'pagination' => [
          'pageSize' => 20,
        ],
      ]),
      'columns' => [
        'model',
        'model_id',
        'points',
        'ts'
      ],
    ]); ?>

  </div>
</div>
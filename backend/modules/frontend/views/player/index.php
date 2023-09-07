<?php

use yii\helpers\Html;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\frontend\models\PlayerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = ucfirst(Yii::$app->controller->module->id) . ' / ' . ucfirst(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => 'Players', 'url' => ['index']];
yii\bootstrap5\Modal::begin([
  'title' => '<h2><i class="bi bi-info-circle-fill"></i> ' . Html::encode($this->title) . ' Help</h2>',
  'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/' . $this->context->action->id), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="player-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <p>
    <?= Html::a('Create Player', ['create'], ['class' => 'btn btn-success']) ?>
    <?= Html::a('Import Players', ['import'], ['class' => 'btn btn-info']) ?>
    <?= Html::a(Yii::t('app', 'Fail Validate'), ['fail-validation'], [
      'class' => 'btn',
      'style' => 'background: #4d246f; color: white;',
      'data' => [
        'confirm' => Yii::t('app', 'This operation validates all the user details are you sure?'),
      ],
    ]) ?>
    <?= Html::a('Reset All player data', ['reset-playdata'], ['class' => 'btn btn-danger', 'data' => ['confirm' => 'Are you sure you want to delete all player data?', 'method' => 'post',]]) ?>
    <?= Html::a('Reset All player progress', ['reset-player-progress'], ['class' => 'btn btn-warning', 'data' => ['confirm' => 'Are you sure you want to delete all player progress?', 'method' => 'post',]]) ?>
  </p>

  <details>
    <summary>Extended Search</summary>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
  </details>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'rowOptions' => function ($model, $key, $index, $grid) {
      // $model is the current data model being rendered
      // check your condition in the if like `if($model->hasMedicalRecord())` which could be a method of model class which checks for medical records.
      $tmpObj=clone($model);
      $tmpObj->scenario='validator';
      if(!$tmpObj->validate()) {
          unset($tmpObj);
          return ['class' => 'text-danger','style'=>'font-weight: 800;'];
      }
      return [];
    },
    'columns' => [
      [
        'attribute' => 'id',
        'headerOptions' => ['style' => 'width:4em'],
      ],
      [
        'attribute' => 'avatar',
        'format' => ['image', ['width' => '40px', 'class' => 'img-thumbnail']],
        'value' => function ($data) {
          return '//' . Yii::$app->sys->offense_domain . '/images/avatars/' . $data->profile->avatar;
        }
      ],
      ['class' => 'app\components\columns\ProfileColumn', 'idkey' => 'profile.id', 'attribute' => 'username', 'field' => 'username'],
      'email:email',
      [
        'attribute' => 'vpn_local_address',
        'label' => 'VPN IP',
        'value' => function ($model) {
          return $model->last && $model->last->vpn_local_address ? long2ip($model->last->vpn_local_address) : null;
        }
      ],
      'online:boolean',
      'active:boolean',
      [
        'attribute' => 'academic',
        'value' => 'academicShort',
        'filter' => [0 => Yii::$app->sys->academic_0short, 1 => Yii::$app->sys->academic_1short, 2 => Yii::$app->sys->academic_2short],
      ],
      [
        'attribute' => 'status',
        'format' => 'playerStatus',
        'filter' => array(10 => 'Enabled', 9 => 'Innactive', 8 => "Change", 0 => "Deleted",),

      ],
      [
        'attribute' => 'type',
        'filter' => ["offense"=>"offense","defense"=>"defense"]
      ],

      'created',
      //'ts',
      [
        'class' => 'yii\grid\ActionColumn',
        'visibleButtons'=>[
          'generate-ssl'=>function($model){ if ($model->status==10 || $model->active==1) return true; return false;},
          'mail'=>function($model){ if ($model->status==10 || $model->active==1) return false; return true;},
          'kill-vpn'=>function($model){ if ($model->last->vpn_local_address!==null) return true; return false;},
          'delete'=>function($model){ if (\Yii::$app->user->identity->isAdmin) return true; return false;},
          'reset-activkey'=>function($model){ if ($model->active && trim($model->activkey)!=="") return true; return false;},
        ],
        'template' => '{player-view-full} {kill-vpn} {view} {generate-ssl} ' . '{update} {delete} {ban} {mail} {reset-activkey}',
        'header' => Html::a(
          '<i class="bi bi-person-fill-exclamation"></i>',
          ['ban-filtered'],
          [
            'title' => 'Mass Delete and ban users',
            'data-pjax' => '0',
            'data-method' => 'POST',
            'data' => [
              'method' => 'post',
              'params' => $searchModel->attributes,
              'confirm' => 'Are you sure you want to delete and ban the currently filtered users?',
            ],
          ]
        ) . ' ' . Html::a(
          '<i class="bi bi-person-dash-fill"></i>',
          ['delete-filtered'],
          [
            'title' => 'Mass Delete users',
            'data-pjax' => '0',
            'data-method' => 'POST',
            'data' => [
              'method' => 'post',
              'params' => $searchModel->attributes,
              'confirm' => 'Are you sure you want to delete the currently filtered users?',
            ],
          ]
        ),
        'buttons' => [
          'reset-activkey' => function($url, $model) {
            return Html::a('<i class="bi bi-archive-fill"></i>', ['reset-activkey', 'id' => $model->id], [
                'class' => '',
                'title'=>'Reset Player activkey',
                'data' => [
                    'confirm' => 'Are you absolutely sure you want to empty the activkey for ['.Html::encode($model->username).'] ?',
                    'method' => 'post',
                  ],
              ]);
          },
          'kill-vpn' => function($url, $model) {
            return Html::a('<i class="bi bi-eraser-fill"></i>', ['kill-vpn', 'id' => $model->id], [
                'class' => '',
                'title'=>'Kill VPN Session',
                'data' => [
                    'confirm' => 'Are you absolutely sure you want to kill the vpn session for ['.Html::encode($model->username).'] ?',
                    'method' => 'post',
                  ],
              ]);
          },
          'delete' => function ($url, $model) {
            return Html::a('<i class="bi bi-trash3-fill"></i>', ['delete', 'id' => $model->id], [
              'class' => '',
              'title'=>'Delete player from the database',
              'data' => [
                'confirm' => 'Are you absolutely sure you want to delete [' . Html::encode($model->username) . '] ?',
                'method' => 'post',
              ],
            ]);
          },
          'generate-ssl' => function ($url) {
            return Html::a(
              '<i class="bi bi-shield-lock-fill"></i>',
              $url,
              [
                'title' => 'Generate SSL Certificates',
                'data-pjax' => '0',
                'data-method' => 'POST',
                'data' => ['confirm' => "Are you sure you want to regenerate the SSL for this user?"]
              ]
            );
          },
          'toggle-academic' => function ($url) {
            return Html::a(
              '<i class="bi bi-building"></i>',
              $url,
              [
                'title' => 'Toggle user academic flag',
                'data-pjax' => '0',
                'data-method' => 'POST',
                'data' => ['confirm' => 'Are you sure you want to toggle the academic flag for this user?']

              ]
            );
          },
          'ban' => function ($url) {
            return Html::a(
              '<i class="bi bi-hammer"></i>',
              $url,
              [
                'title' => 'Delete and ban this user',
                'data-pjax' => '0',
                'data-method' => 'POST',
                'data' => ['confirm' => 'Are you sure you want to delete and ban this user?']
              ]
            );
          },
          'mail' => function ($url) {
            return Html::a(
              '<i class="bi bi-envelope-at-fill"></i>',
              $url,
              [
                'title' => 'Mail this user activation',
                'data-pjax' => '0',
                'data-method' => 'POST',
                'data' => ['confirm' => 'Are you sure you want to mail this user his activation URL?']
              ]
            );
          },
          'player-view-full' => function ($url, $model) {
            $url =  \yii\helpers\Url::to(['/frontend/profile/view-full', 'id' => $model->profile->id]);
            return Html::a(
              '<i class="bi bi-person-lines-fill"></i>',
              $url,
              [
                'title' => 'View full profile',
                'data-pjax' => '0',
              ]
            );
          },

        ],
      ],
    ],
  ]); ?>


</div>
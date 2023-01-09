<?php

use yii\helpers\Html;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\frontend\models\PlayerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title=ucfirst(Yii::$app->controller->module->id).' / '.ucfirst(Yii::$app->controller->id);
$this->params['breadcrumbs'][]=['label' => 'Players', 'url' => ['index']];
yii\bootstrap\Modal::begin([
    'header' => '<h2><span class="glyphicon glyphicon-question-sign"></span> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<span class="glyphicon glyphicon-question-sign"></span> Help','class'=>'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap\Modal::end();
?>
<div class="player-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Player', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Import Players', ['import'], ['class' => 'btn btn-info']) ?>
        <?= Html::a('Reset All player data', ['reset-playdata'], ['class' => 'btn btn-danger', 'data' => ['confirm' => 'Are you sure you want to delete all player data?', 'method' => 'post', ]]) ?>
        <?= Html::a('Reset All player progress', ['reset-player-progress'], ['class' => 'btn btn-warning', 'data' => ['confirm' => 'Are you sure you want to delete all player progress?', 'method' => 'post', ]]) ?>
    </p>

    <details>
    <summary>Extended Search</summary>
    <?php  echo $this->render('_search', ['model' => $searchModel]);?>
    </details>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function ($model, $key, $index, $grid) {
            // $model is the current data model being rendered
            // check your condition in the if like `if($model->hasMedicalRecord())` which could be a method of model class which checks for medical records.
            $model->scenario='validator';
            if(!$model->validate()) {
                 return ['class' => 'text-danger','style'=>'font-weight: 800;'];
            }
            return [];
        },
        'columns' => [
            [
              'attribute'=>'id',
              'headerOptions' => ['style' => 'width:4em'],
            ],
            [
              'attribute'=>'avatar',
              'format'=>'html',
              'value'=>function($data) { return Html::img('//'.Yii::$app->sys->offense_domain.'/images/avatars/' . $data->profile->avatar,['width' => '50px']);}
            ],

            'username',
            'email:email',
            [
              'attribute'=>'vpn_local_address',
              'label'=> 'VPN Local IP',
              'value'=>function($model) { return $model->last && $model->last->vpn_local_address ? long2ip($model->last->vpn_local_address) : null;}
            ],
            'online:boolean',
            'active:boolean',
            [
                'attribute'=>'academic',
                'value'=>'academicShort',
                'filter'=>[0=>Yii::$app->sys->academic_0short,1=>Yii::$app->sys->academic_1short, 2=>Yii::$app->sys->academic_2short],
            ],
            [
             'attribute' => 'status',
             'filter'=>array(10=>'Enabled',9=>'Innactive', 8=>"Change",0=>"Deleted",),

            ],
            'created',
            //'ts',
            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{player-view-full} {view} {generate-ssl} '.'{update} {delete} {ban} {mail}',
              'header' => Html::a(
                  '<span class="glyphicon glyphicon-ban-circle"></span>',
                  ['ban-filtered'],
                  [
                      'title' => 'Mass Delete and ban users',
                      'data-pjax' => '0',
                      'data-method' => 'POST',
                      'data'=>[
                        'method'=>'post',
                        'params'=> $searchModel->attributes,
                        'confirm'=>'Are you sure you want to delete and ban the currently filtered users?',
                      ],
                  ]
              ).' '.Html::a(
                  '<span class="glyphicon glyphicon-trash"></span>',
                  ['delete-filtered'],
                  [
                      'title' => 'Mass Delete users',
                      'data-pjax' => '0',
                      'data-method' => 'POST',
                      'data'=>[
                        'method'=>'post',
                        'params'=> $searchModel->attributes,
                        'confirm'=>'Are you sure you want to delete the currently filtered users?',
                      ],
                  ]
              ),
              'buttons' => [
                  'delete' => function($url, $model) {
                      return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete', 'id' => $model->id], [
                          'class' => '',
                          'data' => [
                              'confirm' => 'Are you absolutely sure you want to delete ['.Html::encode($model->username).'] ?',
                              'method' => 'post',
                          ],
                      ]);
                  },
                  'generate-ssl' => function($url) {
                      return Html::a(
                          '<span class="glyphicon glyphicon-lock"></span>',
                          $url,
                          [
                              'title' => 'Generate SSL Certificates',
                              'data-pjax' => '0',
                              'data-method' => 'POST',
                              'data'=>['confirm'=>"Are you sure you want to regenerate the SSL for this user?"]
                          ]
                      );
                  },
                  'toggle-academic' => function($url) {
                      return Html::a(
                          '<span class="glyphicon glyphicon-education"></span>',
                          $url,
                          [
                              'title' => 'Toggle user academic flag',
                              'data-pjax' => '0',
                              'data-method' => 'POST',
                              'data'=>['confirm'=>'Are you sure you want to toggle the academic flag for this user?']

                          ]
                      );
                  },
                  'ban' => function($url) {
                      return Html::a(
                          '<span class="glyphicon glyphicon-ban-circle"></span>',
                          $url,
                          [
                              'title' => 'Delete and ban this user',
                              'data-pjax' => '0',
                              'data-method' => 'POST',
                              'data'=>['confirm'=>'Are you sure you want to delete and ban this user?']
                          ]
                      );
                  },
                  'mail' => function($url) {
                      return Html::a(
                          '<span class="glyphicon glyphicon-envelope"></span>',
                          $url,
                          [
                              'title' => 'Mail this user activation',
                              'data-pjax' => '0',
                              'data-method' => 'POST',
                              'data'=>['confirm'=>'Are you sure you want to mail this user his activation URL?']
                          ]
                      );
                  },
                  'player-view-full' => function($url, $model) {
                    $url =  \yii\helpers\Url::to(['/frontend/profile/view-full', 'id' => $model->profile->id]);
                    return Html::a(
                        '<span class="glyphicon glyphicon-user"></span>',
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
    ]);?>


</div>

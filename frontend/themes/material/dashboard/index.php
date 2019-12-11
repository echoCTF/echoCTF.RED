<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ListView;
use rce\material\widgets\Card;
use app\components\JustGage;
use yii\widgets\Pjax;
$this->_fluid="-fluid";
$this->title = Yii::$app->sys->event_name .' - Dashboard';
$this->registerCssFile("@web/css/scores.css", [
    'depends' => [\yii\bootstrap\BootstrapAsset::className()],
    'media' => 'screen',
], 'scores-theme');
?>
<div class="dashboard-index">
  <div class="body-content">
    <!-- XXX FIXME XXX ADD MISSING ONLINE COUNTERS -->
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6">
            <?php Card::begin([
                'header'=>'header-icon',
                'type'=>'card-stats',
                'icon'=>'<i class="fa fa-flag"></i>',
                'color'=>'primary',
                'title'=>sprintf('%d/%d',$treasureStats->claimed,$treasureStats->total),
                'subtitle'=>'Claimed/Total Flags',
                'footer'=>'<div class="stats">
                        <i class="material-icons text-danger">flag</i>'.number_format($treasureStats->claims).' total claims
                      </div>',
            ]); Card::end(); ?>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6">
            <?php Card::begin([
                'header'=>'header-icon',
                'type'=>'card-stats',
                'icon'=>'<i class="fa fa-skull"></i>',
                'color'=>'danger',
                'title'=>sprintf('%d',$totalHeadshots),
                'subtitle'=>'Headshots',
                'footer'=>'<div class="stats">
                        <i class="material-icons">memory</i> You have '.count($userHeadshots).' headshots
                      </div>',
            ]); Card::end(); ?>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6">
            <?php Card::begin([
                'header'=>'header-icon',
                'type'=>'card-stats',
                'icon'=>'<i class="fas fa-server"></i>',
                'color'=>'warning',
                'title'=>\app\modules\target\models\Target::find()->active()->count(),
                'subtitle'=>'Targets',
                'footer'=>'<div class="stats">
                        <i class="material-icons">memory</i> '.number_format($totalPoints).' Total points
                      </div>',
            ]); Card::end(); ?>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6">
            <?php Card::begin([
                'type'=>'card-stats',
                'header'=>'header-icon',
                'icon'=>'<i class="fas fa-user-secret"></i>',
                'color'=>'info',
                'title'=>\app\models\Player::find()->active()->count(),
                'subtitle'=>'Users',
                'footer'=>'<div class="stats">
                        <i class="material-icons">update</i> '.\app\models\Player::find()->active()->with_score()->count().' with score
                      </div>',
            ]); Card::end(); ?>
        </div>
    </div>


    <div class="row">
      <div class="col-sm-8">
      <?php Pjax::begin(['id'=>'target-listing','enablePushState'=>false]);?>
      <?php
       echo GridView::widget([
      		'id'=>'target-list',
          'pager'=>[
            'class'=>'yii\bootstrap4\LinkPager',
            'options'=>['id'=>'target-pager','class'=>'align-middle'],
            'firstPageLabel' => '<i class="fas fa-step-backward"></i>',
            'lastPageLabel' => '<i class="fas fa-step-forward"></i>',
            'maxButtonCount'=>3,
            'disableCurrentPageButton'=>true,
            'prevPageLabel'=>'<i class="fas fa-chevron-left"></i>',
            'nextPageLabel'=>'<i class="fas fa-chevron-right"></i>',
          ],
          'options'=>['class'=>'card'],
      		'tableOptions'=>['class'=>'table table-xl'],
          'layout'=>'{summary}<div class="card-body table-responsive">{items}{pager}</div>',
          'dataProvider' => $targetProvider,
      		'summary'=>'<div class="card-header card-header-primary"><h4 class="card-title">Target list</h4><p class="card-category">List of currently available targets</p></div>',
          'columns' => [
            [
      				'label'=>'',
              'contentOptions' => ['class' => 'text-center'],
              'headerOptions' => ['class' => 'text-center',"style"=>'width: 1.5em'],
              'format'=>'raw',
              'value'=>function($model){return sprintf('<img src="/images/targets/_%s.png" alt="%s" class="img-fluid" style="max-width: 20px;max-heigh:30px">',$model->name, $model->fqdn);}
      			],
      			[
      				'attribute'=>'name',
              'label'=>'Target',
      			],
      			[
      				'attribute'=>'ip',
      				'label'=>'IP',
              'headerOptions' => ["style"=>'width: 6vw;'],
      				'value'=>function($model){return long2ip($model->ip);}
      			],
      			[
      				'attribute'=>'difficulty',
              'format'=>'raw',
              'encodeLabel'=>false,
      				'label'=>'<abbr title="Difficulty rating">Difficulty</abbr>',
              'contentOptions' => ['class' => 'd-none d-xl-table-cell'],
              'headerOptions' => ['class' => 'text-center d-none d-xl-table-cell'],
              'value'=>function($model){
                $progress=($model->difficulty*20);
                $color="";
                switch($model->difficulty)
                {
                  case 0:
                    $progress=($model->difficulty*20)+1;
                    $bgcolor="";
                    break;
                  case 1:
                    $bgcolor='bg-info';
                    break;
                  case 2:
                    $bgcolor="bg-primary";
                    break;
                  case 3:
                    $bgcolor="bg-warning";
                    break;
                  case 4:
                    $bgcolor="bg-danger";
                    break;
                  case 5:
                  default:
                }
                return '<center>'.JustGage::widget(['id'=>$model->name.'-'.$model->ip,"htmlOptions"=>['style'=>"width:50px; height:40px"],'options'=>['relativeGaugeSize'=>true,'textRenderer'=>'function (val) {return "";}','min'=>0,'max'=>5,'value'=>$model->difficulty,/*'title'=>$model->difficultyText*/]]).'</center>';
              },
      			],
            [
      				'attribute'=>'rootable',
              'format'=>'raw',
              'headerOptions' => ['class' => 'text-center',"style"=>'width: 4rem'],
              'contentOptions' => ['class' => 'text-center'],
              'encodeLabel'=>false,
              'label'=>'<abbr title="Target rootable or not?"><i class="fa fa-hashtag" aria-hidden="true"></i></abbr>',
              'value'=>function($model){return intval($model->rootable)==0 ? '':'<abbr title="Rootable"><i class="fa fa-hashtag"></i></abbr>';},
      			],
            [
              'format'=>'raw',
              'encodeLabel'=>false,
              'headerOptions' => ["style"=>'width: 4rem','class' => 'text-center'],
              'contentOptions' => ['class' => 'text-center'],
      				'label'=>'<abbr title="Services"><i class="fa fa-fire" aria-hidden="true"></i></abbr>',
              'attribute'=>'total_findings',
              'value'=>function($model) { return '<i class="fas fa-fire"></i> '.count($model->findings); },
      			],
            [
              'format'=>'raw',
              'encodeLabel'=>false,
              'headerOptions' => ["style"=>'width: 4rem','class' => 'text-center'],
              'contentOptions' => ['class' => 'text-center'],
      				'label'=>'<abbr title="Flags"><i class="fa fa-flag" aria-hidden="true"></i></abbr>',
              'attribute'=>'total_treasures',
              'value'=>function($model) { return '<i class="fas fa-flag"></i> '.count($model->treasures); },
      			],
            [
              'format'=>'raw',
              'encodeLabel'=>false,
              'headerOptions' => ["style"=>'width: 4rem','class' => 'text-center'],
              'contentOptions' => ['class' => 'text-center'],
              'attribute'=>'headshots',
      				'label'=>'<abbr title="Number of users who owned all flags and services: Headshots"><i class="fas fa-skull"></i></abbr>',
              'value'=>function($model) { return '<i class="fas fa-skull"></i> '.count($model->headshots); },
      			],
            [
              'format'=>'raw',
              'encodeLabel'=>false,
              'headerOptions' => ['class'=>'text-center d-none d-xl-table-cell'],
              'contentOptions' => ['class'=>'d-none d-xl-table-cell','width'=>'180'],
              'attribute'=>'progress',
              'label'=>'Progress',
              'value'=>function($model) {
                return sprintf ('<div class="progress"><div class="progress-bar bg-gradual-progress" style="width: %d%%" role="progressbar" aria-valuenow="%d" aria-valuemin="0" aria-valuemax="100"></div></div>',$model->progress, $model->progress,$model->progress==100 ? '#Headshot': number_format($model->progress).'%');
                return '<div class="progress"></div>';
              },
            ],
      			[
              'class'=> 'rce\material\grid\ActionColumn',
              'headerOptions' => ["style"=>'width: 4rem'],
      				'template'=>'{spin} {view}',
      				'buttons' => [
      					'spin' => function ($url,$model) {
      							return Html::a(
      									'<i class="material-icons large">power_settings_new</i>',
      									Url::to(['/target/default/spin','id'=>$model->id]),
      									[
                          //'class'=>"btn btn-primary btn-round btn-simple btn-xs",
                          'style'=>"font-size: 1.5em;",
    											'title' => 'Restart container',
    											'data-pjax' => '0',
    											'data-method' => 'POST',
      									]
      							);
      					},
                'view' => function ($url,$model) {
      							return Html::a(
                      '<i class="material-icons">remove_red_eye</i>',
      									Url::to(['/target/default/index','id'=>$model->id]),
      									[
                          'style'=>"font-size: 1.5em;",
                          'rel'=>"tooltip",
//                          'class'=>"btn btn-primary btn-round btn-simple btn-xs",
                          'title' => 'View target',
      										'data-pjax' => '0',
      									]
      							);
      					}
      			],
      			'visibleButtons' => [
          		'spin' => function ($model) {
      						return $model->spinable;
      					},
      			]

      		]
          ],
      ]);

      Pjax::end();?>
      </div>
      <div class="col-sm-4">
        <?php Pjax::begin();
        echo ListView::widget([
            'id'=>'Leaderboard',
            'pager'=>[
              'firstPageLabel' => '<i class="fas fa-step-backward"></i>',
              'lastPageLabel' => '<i class="fas fa-step-forward"></i>',
              'maxButtonCount'=>3,
              'disableCurrentPageButton'=>true,
              'prevPageLabel'=>'<i class="fas fa-chevron-left"></i>',
              'nextPageLabel'=>'<i class="fas fa-chevron-right"></i>',
              'class'=>'yii\bootstrap4\LinkPager',
            ],
            'options'=>['class'=>'card'],
            'dataProvider' => $scoreProvider,
            'layout'=>'{summary}<div class="card-body table-responsive">{items}</div><div class="card-footer">{pager}</div>',
            'summary'=>'<div class="card-header card-header-primary"><h4 class="card-title">Scoreboard</h4><p class="card-category">List of players by points</p></div>',
            'itemOptions' => [
              'tag' => false
            ],
            'itemView' => '_score',
            'viewParams'=>[
              'totalPoints'=>$totalPoints,
            ]
        ]);Pjax::end();
?>
      </div>
    </div><!-- //row -->
    <?php Pjax::begin();
    echo ListView::widget([
        'id'=>'stream',
        'options'=>['class'=>'card'],
        'dataProvider' => $streamProvider,
        'pager'=>[
          'class'=>'yii\bootstrap4\LinkPager',
          'options'=>['class'=>'d-flex align-items-end justify-content-between','id'=>'stream-pager'],
          'firstPageLabel' => '<i class="fas fa-step-backward"></i>',
          'lastPageLabel' => '<i class="fas fa-step-forward"></i>',
          'maxButtonCount'=>3,
          'disableCurrentPageButton'=>true,
          'prevPageLabel'=>'<i class="fas fa-chevron-left"></i>',
          'nextPageLabel'=>'<i class="fas fa-chevron-right"></i>',
        ],
        'layout'=>'{summary}<div class="card-body">{items}</div><div class="card-footer">{pager}</div>',
        'summary'=>'<div class="card-header card-header-primary"><h4 class="card-title">Activity Stream</h4><p class="card-category">Latest activity on the platform</p></div>',
        'itemOptions' => [
          'tag' => false
        ],
        'itemView' => '_stream',
    ]); Pjax::end();

    ?>

  </div><!-- //body-content -->
</div>

<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ListView;

$this->title = Yii::$app->sys->event_name .' - Dashboard';
$this->registerCssFile("@web/css/scores.css", [
    'depends' => [\yii\bootstrap\BootstrapAsset::class],
    'media' => 'screen',
], 'scores-theme');
?>
<div class="dashboard-index">
  <div class="body-content">
	  <h2><?=Yii::$app->sys->event_name?> <span style="color: darkgray; font-size: 0.8em; font-weight: 1">Dashboard</span></h2>
    <!-- XXX FIXME XXX ADD MISSING ONLINE COUNTERS -->
	  <hr/>
    <div class="row">
      <?php

      echo GridView::widget([
          'id'=>'target-list',
          'options'=>['class'=>'col-sm-8'],
          'tableOptions'=>['class'=>'table table-striped'],
          'dataProvider' => $targetProvider,
          'summary'=>'<h4>Target list <span style="display: block;"><small><i>List of currently available targets</i></small></span></h4>',
          'columns' => [
            [
              'attribute'=>'name',
              'header'=>'Target'
            ],
            [
              'attribute'=>'ip',
              'header'=>'IP',
              'value'=>function($model){return long2ip($model->ip);}
            ],
            [
              'attribute'=>'difficultyText',
              'header'=>'<abbr title="Difficulty rating">Difficulty</abbr>'
            ],
            [
              'attribute'=>'rootable',
              'format'=>'boolean',
              'header'=>'<abbr title="Target is rootable or not">Rootable</abbr>',
            ],
            [
              'header'=>'<center><abbr title="Flags"><i class="glyphicon glyphicon-large glyphicon glyphicon-flag"></i></abbr> / <abbr title="Services"><i class="glyphicon glyphicon-fire"></i></abbr> / <abbr title="Number of users who owned all flags and services: Headshots"><i class="glyphicon glyphicon-screenshot"></i></abbr></center>',
              'format'=>'raw',
              'attribute'=>'formattedExtras'
            ],
            [
              'class' => 'yii\grid\ActionColumn',
              'template'=>'{view} {spin}',
              'buttons' => [
                'spin' => function ($url,$model) {
                    return Html::a(
                        '<span class="glyphicon glyphicon glyphicon-off"></span>',
                        Url::to(['/target/default/spin','id'=>$model->id]),
                        [
                            'title' => 'Spin container up/down',
                            'data-pjax' => '0',
                            'data-method' => 'POST',
                        ]
                    );
                },
                'view' => function ($url,$model) {
                    return Html::a(
                        '<span class="glyphicon glyphicon glyphicon-eye-open"></span>',
                        Url::to(['/target/default/index','id'=>$model->id]),
                        [
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
      ?>
      <div class="col-sm-4">
        <?php echo ListView::widget([
            'id'=>'Leaderboard',
            'dataProvider' => $scoreProvider,
            'summary'=>'<h4>Top 10	<span style="display: block;"><small><i>List of top 10 players by points</i></small></span></h4><br/>',
            'itemOptions' => [
              'tag' => false
            ],
            'itemView' => '_score',
            'viewParams'=>[
              'totalPoints'=>$totalPoints,
            ]
        ]);?>
      </div>
    </div>
  </div>
</div>

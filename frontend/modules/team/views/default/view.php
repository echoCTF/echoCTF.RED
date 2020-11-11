<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use app\widgets\stream\StreamWidget as Stream;

$this->title=Yii::$app->sys->event_name.' Details for Team ['.Html::encode($team->name).']';
$this->_fluid="-fluid";

?>
<div class="team-view">
  <div class="body-content">
    <h2>Details for Team [<code><?=Html::encode($team->name)?></code>]</h2>
    <?php if($team->getTeamPlayers()->count()<Yii::$app->sys->members_per_team):?>
    <p>Allow other players to join the team easily by providing them with this link: <code><?=Html::a(Url::to(['/team/default/invite','token'=>$team->token]),['/team/default/invite','token'=>$team->token]);?></code></p>
    <?php else:?>
    <p class="text-warning">Your team is full, you cannot invite any more members</p>
    <?php endif;?>
    <hr />
    <div class="row">
      <div class="col-md-8">
        <?php
        Pjax::begin(['id'=>'stream-listing', 'enablePushState'=>false, 'linkSelector'=>'#stream-pager a', 'formSelector'=>false]);
        echo Stream::widget(['divID'=>'stream', 'dataProvider' => $streamProvider, 'pagerID'=>'stream-pager','category'=>'Latest activity of team on the platform', 'title'=>'Team Activity Stream']);
        Pjax::end();
        ?>
      </div>
      <div class="col-md-4">
        <div class="card card-profile">
          <div class="card-avatar bg-primary">
            <a href="/team/update" alt="Update team details" title="Update team details">
              <img class="img" src="/images/avatars/team/<?=$team->validLogo?>" />
            </a>
          </div>
          <div class="card-body table-responsive">
            <h6 class="badge badge-secondary"><?=$team->score !== NULL ? number_format($team->score->points) : 0?> points</h6>
            <h4 class="card-title"><?=Html::encode($team->name)?></h4>
            <p class="card-description">
              <?=Html::encode($team->description)?>
            </p>
        <?php
        echo GridView::widget([
    //        'id'=>$divID,
            'dataProvider' => $dataProvider,
            'rowOptions'=>function() { },
            'tableOptions'=>['class'=>'table table-xl'],
            'layout'=>'{summary}{items}',
            'summary'=>'',
            'columns' => [
              [
                'headerOptions' => ['class'=>'d-none d-xl-table-cell', ],
                'contentOptions' => ['class'=>'d-none d-xl-table-cell'],
                'attribute'=>'player.username',
                'format'=>'raw',
                'value'=>function($model){ return $model->player->profile->link; },
                'label' => 'Member'
              ],
/*              [
                'attribute'=>'player.email',
                'label' => 'Email',
                'visible' => Yii::$app->user->identity->teamLeader!==null
              ],*/
              'player.playerScore.points:integer',
              'approved:boolean',
              [
                'class'=> 'app\actions\ActionColumn',
                //'visible'=>!$personal,
                'headerOptions' => ["style"=>'width: 4rem'],
                'template'=>'{approve} {reject}',
                'visibleButtons' => [
                  'approve' => function($model) {
                    if($model->approved===0 && Yii::$app->user->identity->teamLeader!==null)
                      return true;
                    return false;
                  },
                  'reject' => function($model) {
                    if((Yii::$app->user->identity->teamLeader!==null /*&& $model->player_id!==Yii::$app->user->id*/) || (Yii::$app->user->identity->teamLeader===null && $model->player_id===Yii::$app->user->id))
                      return true;

                    return false;
                  },
                ],
                'buttons' => [
                  'approve' => function($url, $model) {
                      return Html::a(
                        '<i class="far fa-check-circle"></i>',
                          Url::to(['/team/default/approve', 'id'=>$model->id]),
                          [
                            'style'=>"font-size: 1.5em;",
                            'title' => 'Approve team membership',
                            'rel'=>"tooltip",
                            'data-pjax' => '0',
                            'data-method' => 'POST',
                            'aria-label'=>'Approve team membership',
                          ]
                      );
                  },
                  'reject' => function($url, $model) {
                      $msg='Reject team membership';
                      if($model->player_id===Yii::$app->user->id && Yii::$app->user->identity->teamLeader===null)
                        $msg="Withdraw your team membership";

                      return Html::a(
                        '<i class="far fa-trash-alt"></i>',
                          Url::to(['/team/default/reject', 'id'=>$model->id]),
                          [
                            'data-confirm'=>(Yii::$app->user->identity->teamLeader!==null && $model->player_id===Yii::$app->user->id) ? 'Are you sure you want to proceed? This action will delete the team.':null,
                            'style'=>"font-size: 1.5em;",
                            'title' => $msg,
                            'rel'=>"tooltip",
                            'data-pjax' => '0',
                            'data-method' => 'POST',
                            'aria-label'=>$msg,
                          ]
                      );
                  },
              ],
            ]
          ]
        ]);
        ?>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
?>
<h3><code><?=$profile->headshotsCount?></code> Headshots <?php
$hs=\app\modules\game\models\Headshot::find()->timed()->player_avg_time($profile->player_id)->one();
if($hs && $hs->average > 0)
  echo "/ <small>Average time: ",number_format($hs->average / 60), " minutes";
?> <sub>(ordered by date)</small></sub></h3>
<?php
\yii\widgets\Pjax::begin(['id'=>'headshotslist', 'enablePushState'=>false, 'linkSelector'=>'#headshots-pager a', 'formSelector'=>false]);

echo ListView::widget([
    'dataProvider' => $headshots,
    'options' => [
        'tag' => 'div',
        'class' => 'row',
        'id' => 'headshotslist-wrapper',
    ],
    'layout' => "{items}{pager}",
    'summary'=>false,
    'itemOptions' => [
        'tag' => false
    ],
    'itemView' => '_headshot_item',
    'viewParams' => ['profile' => $profile],
    'pager'=>[
      'class'=>'yii\bootstrap4\LinkPager',
      'linkOptions'=>['class' => ['page-link'], 'aria-label'=>'Pager link','rel'=>'nofollow'],
      'options'=>['id'=>'headshots-pager', 'class'=>'col-md-12 align-middle d-flex d-block'],
      'firstPageLabel' => '<i class="fas fa-step-backward"></i>',
      'lastPageLabel' => '<i class="fas fa-step-forward"></i>',
      'maxButtonCount'=>3,
      'disableCurrentPageButton'=>true,
      'prevPageLabel'=>'<i class="fas fa-chevron-left"></i>',
      'nextPageLabel'=>'<i class="fas fa-chevron-right"></i>',
    ],
]);
\yii\widgets\Pjax::end();

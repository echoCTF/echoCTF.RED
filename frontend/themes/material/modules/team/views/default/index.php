<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ListView;

$this->title=Yii::$app->sys->event_name.' Teams';
$this->_fluid="-fluid";

?>

<div class="team-index">
  <div class="body-content">
    <h2><?=Html::encode($this->title)?></h2>
    <?php if( Yii::$app->user->identity->team===null):?>Join a team or <b><?= Html::a('Create', ['/team/default/create'],['class'=>'btn btn-info btn-sm']) ?></b> a new one!<?php endif;?>
    <hr />
    <div class="card-deck d-flex justify-content-center">
    <?php
    $colsCount = 3;
    echo ListView::widget([
          'dataProvider' => $dataProvider,
          'emptyText'=>'<p class="text-warning"><b>Oh! no, there are no teams... Quickly create one :)</b></p>',
          'options' => [
              'tag' => false,
          ],
          'itemOptions' => [
              'tag' => 'div',
              'class'=>"col d-flex align-items-stretch flex-column",
          ],
          'summary'=>false,
          'itemView' => '_team_card',
          'viewParams'=>['invite'=>false],
          'beforeItem' => function ($model, $key, $index, $widget) use ($colsCount) {
              if ($index % $colsCount === 0) {
                  return "<div class='row'>";
              }
          },
          'afterItem' => function ($model, $key, $index, $widget) use ($colsCount) {
              $content = '';
              if (($index > 0) && ($index % $colsCount === $colsCount - 1)) {
                  $content .= "</div>";
              }
              return $content;
          },
      ]);
      if ($dataProvider->count % $colsCount !== 0) {
          echo "</div>";
      }
      ?>
      </div>
  </div>
</div>

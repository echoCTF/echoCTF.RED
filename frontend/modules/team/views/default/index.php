<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ListView;

$this->title=Yii::$app->sys->event_name.' - Teams'.  ( Yii::$app->user->identity->academic===1 ? ' (Academic)': ' (Professionals)' );
$this->_fluid="-fluid";

?>
<div class="team-index">
  <div class="body-content">
    <h2><?=Html::encode($this->title)?></h2>
    Join a team<?php if( Yii::$app->user->identity->team===null):?> or <b><?= Html::a('Create', ['/team/default/create']) ?></b> a new one<?php endif;?>!
    <hr />

    <?php
    $colsCount = 3;
    echo ListView::widget([
          'dataProvider' => $dataProvider,
          'options' => [
              'tag' => false,
          ],
          'itemOptions' => [
              'tag' => 'div',
              'class'=>"col-md-4",
          ],
          'summary'=>false,
          'itemView' => '_team_card',
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

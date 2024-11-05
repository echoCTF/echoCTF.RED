<?php

use yii\widgets\Pjax;
use app\widgets\Card;
use app\widgets\target\TargetWidget;
use app\widgets\leaderboard\Leaderboard;
use app\widgets\stream\StreamWidget as Stream;
use yii\widgets\ListView;

$this->_fluid = "-fluid";
$this->title = Yii::$app->sys->event_name . ' Speed programming';
$this->_description = Yii::$app->sys->event_name . ' Speed programming problems';
$hidden_attributes = ['id'];
?>
<h4 id="countdown"></h4>

<div class="target-index">
  <div class="body-content">
    <h2>Speed Programming Problems</h2>
    <?= \Yii::t('app',"Speed programming problems for you to solve.") ?>
    <?=
    ListView::widget([
      'options' => ['class' => 'list-view row'],
      'itemOptions' => [
        'tag' => false,
      ],
      'dataProvider' => $dataProvider,
      'layout' => "{items}",
      'summary' => false,
      'pager' => false,
      'itemView' => '_problem_card',
    ]);
    ?>

  </div><!-- //body-content -->
</div>
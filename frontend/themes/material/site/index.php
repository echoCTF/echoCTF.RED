<?php
/* @var $this yii\web\View */
$this->title=Yii::$app->sys->event_name;

?>
<div class="site-index">
    <div class="body-content">
      <?php
      if(Yii::$app->user->isGuest)
        echo Yii::$app->sys->frontpage_scenario;
      else
        echo Yii::$app->sys->offense_scenario;
      ?>
    </div>
</div>

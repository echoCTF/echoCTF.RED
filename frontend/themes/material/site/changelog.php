<?php
use yii\helpers\Markdown;
$this->title=Yii::$app->sys->event_name.' Changelog';
$this->_description="The ".\Yii::$app->sys->{"event_name"}." Changelog";

?>

<div class="changelog-index">
  <div class="body-content">
    <?=Markdown::process($changelog);?>

    <?=Markdown::process($todo);?>
  </div>
</div>

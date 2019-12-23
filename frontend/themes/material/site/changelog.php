<?php
use yii\helpers\Markdown;
$this->title = Yii::$app->sys->event_name .' Changelog';
$this->_description="The echoCTF.RED Changelog";

?>

<div class="changelog-index">
  <div class="body-content">
    <h2>Changelog</h2>
    <p>Scheduled and completed changes on the platform.</p>

    <?=Markdown::process($changelog);?>
    <?=Markdown::process($todo);?>

  </div>
</div>

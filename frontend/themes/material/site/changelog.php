<?php
use yii\helpers\Markdown;
$this->title = Yii::$app->sys->event_name .' Changelog';
$this->_description="The echoCTF.RED Changelog";

?>

<div class="changelog-index">
  <div class="body-content">
    <?=Markdown::process($content);?>
  </div>
</div>

<?php
/* @var $this yii\web\View */
$this->title=\Yii::$app->sys->event_name.' '.$content->title;
?>
<div class="static-page-<?=$content->slug?>">
  <div class="body-content">
<?=$content->body?>
  </div>
</div>

<?php
if ($model->owner->status === 0) {
  $streams = $model->owner->archivedStreams;
  $TITLE="Archived activity stream";
} else {
  $streams = $model->owner->streams;
  $TITLE="Activity stream";
}
?>
<h5><?=$TITLE?></h5>
<p><?php
    $i = 1;
    foreach ($streams as $stream) {
      echo "<small>", $stream->formatted, " <sub>", Yii::$app->formatter->asRelativeTime($stream->ts), "</sub></small><br/>";
      if (($i++) > 20) break;
    }
    ?></p>
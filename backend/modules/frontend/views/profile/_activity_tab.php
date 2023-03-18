<h5>Activity Stream</h5>

<p>
<?php
$i=1;
foreach($model->owner->streams as $stream)
{
  echo "<small>",$stream->formatted," <sub>",Yii::$app->formatter->asRelativeTime($stream->ts),"</sub></small><br/>";
  if(($i++)>20) break;
}
?>
</p>

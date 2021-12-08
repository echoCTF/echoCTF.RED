<p>
<?php
$i=1;
foreach($model->owner->streams as $stream)
{
  echo "<small>",$stream->formatted," ",$stream->ts,"</small><br/>";
  if(($i++)>20) break;
}
?>
</p>

<p>
<?php
foreach($model->owner->streams as $stream)
  echo "<small>",$stream->formatted," ",$stream->ts,"</small><br/>";
?>
</p>

<?php
use yii\helpers\Markdown;
?>

<div class="changelog-index">
  <div class="body-content">
    <?=Markdown::process($content);?>
  </div>
</div>

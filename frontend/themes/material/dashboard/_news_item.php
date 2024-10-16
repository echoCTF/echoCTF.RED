<?php
use yii\helpers\HtmlPurifier;
?>
<div class="news-item">
    <h4 class="text-warning"><?=$model->category;?> <?= HtmlPurifier::process($model->title) ?></h4>
    <p><?= HtmlPurifier::process($model->body) ?></p>
</div>

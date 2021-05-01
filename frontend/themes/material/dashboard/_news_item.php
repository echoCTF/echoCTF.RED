<?php
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
?>
<div class="news-item">
    <h4 class="text-primary"><?= Html::encode($model->title) ?></h4>
    <p><?= HtmlPurifier::process($model->body) ?> <small><?=\Yii::$app->formatter->asRelativeTime($model->created_at)?></small></p>
</div>

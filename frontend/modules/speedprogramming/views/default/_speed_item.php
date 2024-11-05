<?php
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
?>
<div class="speedItem">
    <h4><?=$index+1?>. <?=Html::encode($model->player->username)?>, programming language: <?= Html::encode($model->language) ?>, status: <?= Html::encode($model->status) ?>, points: <?= Html::encode($model->points) ?></h4>
</div>

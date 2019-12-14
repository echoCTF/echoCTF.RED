<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
?>
<div class="faq-view">
    <h3><b><?=Html::encode($model->title)?></b></h3>
    <?=$model->body;?>
</div>

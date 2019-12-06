<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
?>
<div class="rule-view">
    <h3><b><?= Html::encode($model->title)?></b></h3>
    <p><?=$model->message;?></p>
</div>
